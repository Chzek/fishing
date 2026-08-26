<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EntitySoftDeleteTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_soft_delete_and_restore_record()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();
        $record = Record::create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 18.0,
            'caught' => now()->format('Y-m-d'),
        ]);

        // Soft delete
        $response = $this->actingAs($admin)->delete("/record/{$record->id}");
        $response->assertRedirect('/record');
        $this->assertSoftDeleted('records', ['id' => $record->id]);

        // Access Trash Bin
        $trashResponse = $this->actingAs($admin)->get('/admin/trash');
        $trashResponse->assertStatus(200);

        // Restore
        $restoreResponse = $this->actingAs($admin)->post('/admin/trash/restore', [
            'type' => 'record',
            'id' => $record->id,
        ]);
        $restoreResponse->assertRedirect(route('admin.trash'));
        $this->assertDatabaseHas('records', ['id' => $record->id, 'deleted_at' => null]);
    }

    public function test_can_permanently_force_delete_lure_from_trash()
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $lure = Lure::create([
            'name' => 'Mepps Aglia',
            'color' => 'Silver',
            'size' => '#3',
        ]);

        // Soft delete
        $this->actingAs($admin)->delete("/lure/{$lure->id}");
        $this->assertSoftDeleted('lures', ['id' => $lure->id]);

        // Force delete
        $forceResponse = $this->actingAs($admin)->delete('/admin/trash/force-delete', [
            'type' => 'lure',
            'id' => $lure->id,
        ]);

        $forceResponse->assertRedirect(route('admin.trash'));
        $this->assertDatabaseMissing('lures', ['id' => $lure->id]);
    }
}
