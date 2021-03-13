<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

use Fishinglog\Record;

class RecordTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that a Notification is raised when a Record is Created..
     *
     * @return void
     */
    public function testCreateRecord()
    {
        $user = factory(\Fishinglog\User::class)->create();
        $this->be($user);

        $this->assertDatabaseHas('notifications', ["id" => 1]);
    }
}
