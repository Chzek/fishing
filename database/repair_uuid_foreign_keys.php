<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$backupPath = database_path('backups/fishing_backup_2026_08_09_212743.sql.gz');

if (!file_exists($backupPath)) {
    echo "Backup file not found!\n";
    exit(1);
}

echo "Reading SQL backup file...\n";
$gz = gzopen($backupPath, 'r');
$sqlContent = '';
while (!gzeof($gz)) {
    $sqlContent .= gzgets($gz, 4096);
}
gzclose($gz);

echo "Backup loaded. Building ID mappings...\n";

// Map: [table => [oldIntId => currentUuid]]
$idMap = [];

// 1. Map Users by email
if (preg_match("/INSERT INTO `users` VALUES (.*?);/s", $sqlContent, $matches)) {
    preg_match_all("/\((\d+),'([^']+)',/s", $matches[1], $rows, PREG_SET_ORDER);
    foreach ($rows as $r) {
        $oldId = $r[1];
        $email = $r[2];
        $user = DB::table('users')->where('email', $email)->first();
        if ($user) {
            $idMap['users'][(string)$oldId] = $user->id;
        }
    }
}

// 2. Map Anglers by firstName and lastName
if (preg_match("/INSERT INTO `anglers` VALUES (.*?);/s", $sqlContent, $matches)) {
    preg_match_all("/\((\d+),'([^']*)','([^']*)',/s", $matches[1], $rows, PREG_SET_ORDER);
    foreach ($rows as $r) {
        $oldId = $r[1];
        $firstName = $r[2];
        $lastName = $r[3];
        $angler = DB::table('anglers')->where('firstName', $firstName)->where('lastName', $lastName)->first();
        if ($angler) {
            $idMap['anglers'][(string)$oldId] = $angler->id;
        }
    }
}

// 3. Map Lakes by name
if (preg_match("/INSERT INTO `lakes` VALUES (.*?);/s", $sqlContent, $matches)) {
    preg_match_all("/\((\d+),'((?:\\\\\'|[^\'])*)',/s", $matches[1], $rows, PREG_SET_ORDER);
    foreach ($rows as $r) {
        $oldId = $r[1];
        $name = str_replace("\\'", "'", $r[2]);
        $lake = DB::table('lakes')->where('name', $name)->first();
        if ($lake) {
            $idMap['lakes'][(string)$oldId] = $lake->id;
        }
    }
}

// 4. Map Fish Breeds by name
if (preg_match("/INSERT INTO `fish_breeds` VALUES (.*?);/s", $sqlContent, $matches)) {
    preg_match_all("/\((\d+),\d+,'((?:\\\\\'|[^\'])*)',/s", $matches[1], $rows, PREG_SET_ORDER);
    foreach ($rows as $r) {
        $oldId = $r[1];
        $name = str_replace("\\'", "'", $r[2]);
        $breed = DB::table('fish_breeds')->where('name', $name)->first();
        if ($breed) {
            $idMap['fish_breeds'][(string)$oldId] = $breed->id;
        }
    }
}

// 5. Map Lures by name
if (preg_match("/INSERT INTO `lures` VALUES (.*?);/s", $sqlContent, $matches)) {
    preg_match_all("/\((\d+),'((?:\\\\\'|[^\'])*)',/s", $matches[1], $rows, PREG_SET_ORDER);
    foreach ($rows as $r) {
        $oldId = $r[1];
        $name = str_replace("\\'", "'", $r[2]);
        $lure = DB::table('lures')->where('name', $name)->first();
        if ($lure) {
            $idMap['lures'][(string)$oldId] = $lure->id;
        }
    }
}

// 6. Map Expeditions by title or description
if (preg_match("/INSERT INTO `expeditions` VALUES (.*?);/s", $sqlContent, $matches)) {
    preg_match_all("/\((\d+),'((?:\\\\\'|[^\'])*)',/s", $matches[1], $rows, PREG_SET_ORDER);
    foreach ($rows as $r) {
        $oldId = $r[1];
        $desc = str_replace("\\'", "'", $r[2]);
        $expedition = DB::table('expeditions')->where('description', $desc)->first();
        if ($expedition) {
            $idMap['expeditions'][(string)$oldId] = $expedition->id;
        }
    }
}

// 7. Map Fish Families by name
if (preg_match("/INSERT INTO `fish_families` VALUES (.*?);/s", $sqlContent, $matches)) {
    preg_match_all("/\((\d+),'((?:\\\\\'|[^\'])*)',/s", $matches[1], $rows, PREG_SET_ORDER);
    foreach ($rows as $r) {
        $oldId = $r[1];
        $name = str_replace("\\'", "'", $r[2]);
        $family = DB::table('fish_families')->where('name', $name)->first();
        if ($family) {
            $idMap['fish_families'][(string)$oldId] = $family->id;
        }
    }
}

echo "ID Mappings created:\n";
foreach ($idMap as $tbl => $map) {
    echo "  Table {$tbl}: " . count($map) . " mappings\n";
}

echo "Updating foreign keys in database...\n";

// Update anglers.user_id
if (isset($idMap['users'])) {
    foreach ($idMap['users'] as $oldId => $newUuid) {
        DB::table('anglers')->where('user_id', (string)$oldId)->update(['user_id' => $newUuid]);
    }
}

// Update fish_breeds.fish_families_id
if (isset($idMap['fish_families'])) {
    foreach ($idMap['fish_families'] as $oldId => $newUuid) {
        DB::table('fish_breeds')->where('fish_families_id', (string)$oldId)->update(['fish_families_id' => $newUuid]);
    }
}

// Update records foreign keys
if (isset($idMap['anglers'])) {
    foreach ($idMap['anglers'] as $oldId => $newUuid) {
        DB::table('records')->where('anglers_id', (string)$oldId)->update(['anglers_id' => $newUuid]);
        DB::table('crews')->where('anglers_id', (string)$oldId)->update(['anglers_id' => $newUuid]);
        DB::table('posts')->where('anglers_id', (string)$oldId)->update(['anglers_id' => $newUuid]);
    }
}

if (isset($idMap['lakes'])) {
    foreach ($idMap['lakes'] as $oldId => $newUuid) {
        DB::table('records')->where('lakes_id', (string)$oldId)->update(['lakes_id' => $newUuid]);
        DB::table('lake_daily_weather')->where('lakes_id', (string)$oldId)->update(['lakes_id' => $newUuid]);
    }
}

if (isset($idMap['fish_breeds'])) {
    foreach ($idMap['fish_breeds'] as $oldId => $newUuid) {
        DB::table('records')->where('fish_breeds_id', (string)$oldId)->update(['fish_breeds_id' => $newUuid]);
    }
}

if (isset($idMap['lures'])) {
    foreach ($idMap['lures'] as $oldId => $newUuid) {
        DB::table('records')->where('lures_id', (string)$oldId)->update(['lures_id' => $newUuid]);
    }
}

if (isset($idMap['expeditions'])) {
    foreach ($idMap['expeditions'] as $oldId => $newUuid) {
        DB::table('crews')->where('expeditions_id', (string)$oldId)->update(['expeditions_id' => $newUuid]);
        DB::table('posts')->where('expeditions_id', (string)$oldId)->update(['expeditions_id' => $newUuid]);
    }
}

echo "✓ Foreign key relationships successfully restored!\n";
