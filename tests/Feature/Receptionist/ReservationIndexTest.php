<?php

namespace Tests\Feature\Receptionist;

use App\Models\Client;
use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReservationIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('manager', 'web');
        Role::findOrCreate('receptionist', 'web');
        Role::findOrCreate('client', 'web');
    }

    public function test_receptionist_can_view_client_reservations(): void
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('receptionist');

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $clientUser = User::factory()->create([
            'name' => 'Reserved Client',
            'email' => 'reserved@example.com',
            'is_approved' => true,
            'approved_by' => $receptionist->id,
        ]);
        $clientUser->assignRole('client');

        $client = Client::create([
            'user_id' => $clientUser->id,
            'name' => $clientUser->name,
            'email' => $clientUser->email,
            'country' => 'Egypt',
            'gender' => 'male',
            'is_approved' => true,
            'approved_by' => $receptionist->id,
        ]);

        $floor = Floor::create([
            'name' => 'First Floor',
            'number' => '1001',
            'manager_id' => $manager->id,
        ]);

        $room = Room::create([
            'number' => '101',
            'capacity' => 2,
            'price' => 15000,
            'floor_id' => $floor->id,
            'manager_id' => $manager->id,
        ]);

        $reservation = Reservation::create([
            'client_id' => $client->id,
            'room_id' => $room->id,
            'accompany_number' => 3,
            'paid_price' => 25999,
            'reservation_date' => now()->subDay(),
        ]);

        $response = $this
            ->actingAs($receptionist)
            ->get(route('receptionist.reservations.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Receptionist/Reservations/Index')
            ->has('reservations.data', 1)
            ->where('reservations.data.0.id', $reservation->id)
            ->where('reservations.data.0.client_name', 'Reserved Client')
            ->where('reservations.data.0.room_number', '101')
            ->where('reservations.data.0.accompany_number', 3)
            ->where('reservations.data.0.paid_price_dollars', '259.99')
        );
    }

    public function test_non_receptionists_cannot_view_reservations(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $this->actingAs($manager)
            ->get(route('receptionist.reservations.index'))
            ->assertForbidden();
    }
}
