<?php

namespace Tests\Feature\Manager;

use App\Models\Client;
use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoomManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('manager', 'web');
    }

    public function test_manager_can_create_room_and_price_is_stored_in_cents(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $floor = Floor::query()->create([
            'name' => 'North Wing',
            'number' => '1001',
            'manager_id' => $manager->id,
        ]);

        $response = $this
            ->actingAs($manager)
            ->post(route('manager.rooms.store'), [
                'number' => '4001',
                'capacity' => 3,
                'price' => '75.50',
                'floor_id' => $floor->id,
            ]);

        $response->assertRedirect(route('manager.rooms.index'));
        $response->assertSessionHas('success', 'Room created successfully.');

        $this->assertDatabaseHas('rooms', [
            'number' => '4001',
            'capacity' => 3,
            'price' => 7550,
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);
    }

    public function test_room_with_reservations_cannot_be_deleted(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $floor = Floor::query()->create([
            'name' => 'North Wing',
            'number' => '1001',
            'manager_id' => $manager->id,
        ]);

        $room = Room::query()->create([
            'number' => '4001',
            'capacity' => 3,
            'price' => 7550,
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        $client = Client::query()->create([
            'name' => 'Client One',
            'email' => 'client@example.com',
            'country' => 'EG',
            'gender' => 'male',
        ]);

        Reservation::query()->create([
            'client_id' => $client->id,
            'room_id' => $room->id,
            'accompany_number' => 1,
            'paid_price' => 7550,
            'reservation_date' => now(),
        ]);

        $response = $this
            ->actingAs($manager)
            ->from(route('manager.rooms.index'))
            ->delete(route('manager.rooms.destroy', $room));

        $response->assertRedirect(route('manager.rooms.index'));
        $response->assertSessionHasErrors('room');
        $this->assertDatabaseHas('rooms', ['id' => $room->id]);
    }
}
