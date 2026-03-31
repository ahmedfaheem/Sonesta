<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReservationDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('manager', 'web');
        Role::findOrCreate('client', 'web');
    }

    public function test_client_can_delete_their_own_reservation(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $clientUser = User::factory()->create([
            'is_approved' => true,
        ]);
        $clientUser->assignRole('client');

        $client = Client::create([
            'user_id' => $clientUser->id,
            'name' => $clientUser->name,
            'email' => $clientUser->email,
            'country' => 'Egypt',
            'gender' => 'male',
            'is_approved' => true,
        ]);

        $floor = Floor::create([
            'name' => 'First Floor',
            'number' => '1001',
            'manager_id' => $manager->id,
        ]);

        $room = Room::create([
            'number' => '101',
            'capacity' => 2,
            'price' => 10000,
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        $reservation = Reservation::create([
            'client_id' => $client->id,
            'room_id' => $room->id,
            'accompany_number' => 1,
            'paid_price' => 10000,
            'reservation_date' => now(),
        ]);

        $this->actingAs($clientUser)
            ->delete(route('client.reservations.destroy', $reservation))
            ->assertRedirect();

        $this->assertDatabaseMissing('reservations', [
            'id' => $reservation->id,
        ]);
    }

    public function test_client_cannot_delete_another_clients_reservation(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $ownerUser = User::factory()->create([
            'is_approved' => true,
        ]);
        $ownerUser->assignRole('client');

        $ownerClient = Client::create([
            'user_id' => $ownerUser->id,
            'name' => $ownerUser->name,
            'email' => $ownerUser->email,
            'country' => 'Egypt',
            'gender' => 'male',
            'is_approved' => true,
        ]);

        $otherUser = User::factory()->create([
            'is_approved' => true,
        ]);
        $otherUser->assignRole('client');

        Client::create([
            'user_id' => $otherUser->id,
            'name' => $otherUser->name,
            'email' => $otherUser->email,
            'country' => 'Egypt',
            'gender' => 'male',
            'is_approved' => true,
        ]);

        $floor = Floor::create([
            'name' => 'First Floor',
            'number' => '1001',
            'manager_id' => $manager->id,
        ]);

        $room = Room::create([
            'number' => '101',
            'capacity' => 2,
            'price' => 10000,
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        $reservation = Reservation::create([
            'client_id' => $ownerClient->id,
            'room_id' => $room->id,
            'accompany_number' => 1,
            'paid_price' => 10000,
            'reservation_date' => now(),
        ]);

        $this->actingAs($otherUser)
            ->delete(route('client.reservations.destroy', $reservation))
            ->assertForbidden();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
        ]);
    }
}