<?php

namespace Tests\Feature\Manager;

use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FloorManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('manager', 'web');
    }

    public function test_manager_only_sees_their_own_floors(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $otherManager = User::factory()->create();
        $otherManager->assignRole('manager');

        $ownFloor = Floor::query()->create([
            'name' => 'North Wing',
            'number' => '1001',
            'manager_id' => $manager->id,
        ]);

        Floor::query()->create([
            'name' => 'South Wing',
            'number' => '2002',
            'manager_id' => $otherManager->id,
        ]);

        $response = $this
            ->actingAs($manager)
            ->get(route('manager.floors.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Manager/Floors/Index')
            ->has('floors.data', 1)
            ->where('floors.data.0.id', $ownFloor->id)
        );
    }

    public function test_floor_with_rooms_cannot_be_deleted(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $floor = Floor::query()->create([
            'name' => 'North Wing',
            'number' => '1001',
            'manager_id' => $manager->id,
        ]);

        Room::query()->create([
            'number' => '3001',
            'capacity' => 2,
            'price' => 5000,
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        $response = $this
            ->actingAs($manager)
            ->from(route('manager.floors.index'))
            ->delete(route('manager.floors.destroy', $floor));

        $response->assertRedirect(route('manager.floors.index'));
        $response->assertSessionHasErrors('floor');
        $this->assertDatabaseHas('floors', ['id' => $floor->id]);
    }
}
