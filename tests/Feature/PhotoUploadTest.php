<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Photo;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhotoUploadTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    #[Test]
    public function record_can_be_created_without_photos()
    {
        $user = User::factory()->create();
        $family = FishFamily::create(['name' => 'Salmonidae']);
        $breed = FishBreed::create(['name' => 'Lake Trout', 'fish_families_id' => $family->id]);
        $lake = Lake::factory()->create();
        $angler = Angler::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/record', [
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'caught' => '2026-08-15',
            'length' => 24.5,
            'weight' => 5.2,
            'released' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('records', [
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'length' => 24.5,
        ]);
        $this->assertEquals(0, Photo::count());
    }

    #[Test]
    public function record_can_be_created_with_optional_photos()
    {
        $user = User::factory()->create();
        $family = FishFamily::create(['name' => 'Esocidae']);
        $breed = FishBreed::create(['name' => 'Northern Pike', 'fish_families_id' => $family->id]);
        $lake = Lake::factory()->create();
        $angler = Angler::factory()->create(['user_id' => $user->id]);

        $file1 = UploadedFile::fake()->image('pike_hero.jpg', 1200, 800);
        $file2 = UploadedFile::fake()->image('pike_tape.jpg', 1200, 800);

        $response = $this->actingAs($user)->post('/record', [
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'caught' => '2026-08-15',
            'length' => 41.0,
            'weight' => 18.5,
            'released' => 1,
            'photos' => [$file1, $file2],
        ]);

        $response->assertRedirect();
        $record = Record::where('length', 41.0)->first();
        $this->assertNotNull($record);

        $photos = $record->photos;
        $this->assertCount(2, $photos);
        $this->assertTrue($photos->first()->is_cover);

        Storage::disk('public')->assertExists($photos->first()->path);
        Storage::disk('public')->assertExists($photos->last()->path);
    }

    #[Test]
    public function photos_can_be_uploaded_to_expedition_gallery()
    {
        $user = User::factory()->create();
        $expedition = Expedition::create([
            'description' => 'Wawa Wilderness Expedition 2026',
            'start' => '2026-08-10',
            'finish' => '2026-08-16',
        ]);

        $file = UploadedFile::fake()->image('camp_sunset.jpg', 1600, 900);

        $response = $this->actingAs($user)->post(route('photos.store'), [
            'photoable_type' => 'expedition',
            'photoable_id' => $expedition->id,
            'photos' => [$file],
            'caption' => 'Camp sunset on Dog Lake',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('photos', [
            'photoable_type' => Expedition::class,
            'photoable_id' => $expedition->id,
            'caption' => 'Camp sunset on Dog Lake',
        ]);

        $expedition->refresh();
        $this->assertCount(1, $expedition->photos);
        $this->assertEquals('Camp sunset on Dog Lake', $expedition->coverPhoto()->caption);
    }

    #[Test]
    public function user_can_set_catch_photo_as_profile_avatar()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create(['user_id' => $user->id]);
        $record = Record::factory()->create(['anglers_id' => $angler->id]);

        $photo = Photo::create([
            'photoable_type' => Record::class,
            'photoable_id' => $record->id,
            'path' => 'photos/records/trophy.jpg',
            'original_name' => 'trophy.jpg',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('photos.avatar', $photo));

        $response->assertRedirect();
        $angler->refresh();
        $this->assertEquals('photos/records/trophy.jpg', $angler->avatar);
    }

    #[Test]
    public function photo_can_be_deleted()
    {
        $user = User::factory()->create();
        $photo = Photo::create([
            'photoable_type' => Record::class,
            'photoable_id' => 'fake-uuid',
            'path' => 'photos/records/sample.jpg',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('photos.destroy', $photo));

        $response->assertRedirect();
        $this->assertSoftDeleted('photos', ['id' => $photo->id]);
    }

    #[Test]
    public function uploaded_storage_media_can_be_retrieved_via_storage_route()
    {
        Storage::disk('public')->put('photos/test_image.jpg', 'dummy image content');

        $response = $this->get('/storage/photos/test_image.jpg');
        $response->assertStatus(200);
    }
}
