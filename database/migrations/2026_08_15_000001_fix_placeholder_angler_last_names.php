<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $anglers = DB::table('anglers')
            ->join('users', 'anglers.user_id', '=', 'users.id')
            ->where('anglers.lastName', 'Angler')
            ->select('anglers.id', 'users.name')
            ->get();

        foreach ($anglers as $angler) {
            $nameParts = explode(' ', trim($angler->name), 2);
            $firstName = $nameParts[0] ?? $angler->name;
            $lastName = $nameParts[1] ?? '';

            DB::table('anglers')
                ->where('id', $angler->id)
                ->update([
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
