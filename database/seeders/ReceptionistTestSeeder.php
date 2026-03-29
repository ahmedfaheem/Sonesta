<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReceptionistTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $receptionists = collect([
            [
                'name' => 'Receptionist One',
                'email' => 'receptionist1@test.com',
                'national_id' => '90000000000001',
            ],
            [
                'name' => 'Receptionist Two',
                'email' => 'receptionist2@test.com',
                'national_id' => '90000000000002',
            ],
        ])->map(fn (array $attributes) => $this->createReceptionist($attributes));

        $manager = $this->createManager();
        $rooms = $this->ensureRooms($manager);

        foreach (range(1, 5) as $index) {
            $this->createClient([
                'name' => "Pending Client {$index}",
                'email' => "pendingclient{$index}@test.com",
                'national_id' => sprintf('910000000000%02d', $index),
                'country' => $index % 2 === 0 ? 'France' : 'Egypt',
                'gender' => $index % 2 === 0 ? 'female' : 'male',
                'is_approved' => false,
                'approved_by' => null,
            ]);
        }

        $approvedClients = collect([
            [
                'name' => 'Approved Client 1',
                'email' => 'approvedclient1@test.com',
                'national_id' => '92000000000001',
                'country' => 'Egypt',
                'gender' => 'male',
                'is_approved' => true,
                'approved_by' => $receptionists[0]->id,
            ],
            [
                'name' => 'Approved Client 2',
                'email' => 'approvedclient2@test.com',
                'national_id' => '92000000000002',
                'country' => 'Italy',
                'gender' => 'female',
                'is_approved' => true,
                'approved_by' => $receptionists[0]->id,
            ],
            [
                'name' => 'Approved Client 3',
                'email' => 'approvedclient3@test.com',
                'national_id' => '92000000000003',
                'country' => 'Spain',
                'gender' => 'male',
                'is_approved' => true,
                'approved_by' => $receptionists[1]->id,
            ],
        ])->map(fn (array $attributes) => $this->createClient($attributes));

        $approvedClients->each(function (User $user, int $index) use ($rooms): void {
            $client = $user->clientProfile;

            if (! $client) {
                return;
            }

            foreach (range(1, 2) as $reservationIndex) {
                $room = $rooms[($index + $reservationIndex - 1) % $rooms->count()];

                Reservation::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'room_id' => $room->id,
                        'reservation_date' => now()->subDays(($index * 2) + $reservationIndex),
                    ],
                    [
                        'accompany_number' => ($index + $reservationIndex) % 3,
                        'paid_price' => $room->price + (($index + 1) * 2500) + ($reservationIndex * 1250),
                    ],
                );
            }
        });
    }

    protected function createReceptionist(array $attributes): User
    {
        $user = User::updateOrCreate(
            ['email' => $attributes['email']],
            [
                'name' => $attributes['name'],
                'password' => Hash::make('123456'),
                'national_id' => $attributes['national_id'],
                'is_approved' => true,
                'approved_by' => null,
            ],
        );

        if (! $user->hasRole('receptionist')) {
            $user->assignRole('receptionist');
        }

        return $user;
    }

    protected function createManager(): User
    {
        $manager = User::firstOrCreate(
            ['email' => 'manager.seed@test.com'],
            [
                'name' => 'Seed Manager',
                'password' => Hash::make('123456'),
                'national_id' => '93000000000001',
            ],
        );

        if (! $manager->hasRole('manager')) {
            $manager->assignRole('manager');
        }

        return $manager;
    }

    protected function ensureRooms(User $manager)
    {
        $floorOne = Floor::firstOrCreate(
            ['number' => '1001'],
            [
                'name' => 'Seed Floor One',
                'manager_id' => $manager->id,
            ],
        );

        $floorTwo = Floor::firstOrCreate(
            ['number' => '1002'],
            [
                'name' => 'Seed Floor Two',
                'manager_id' => $manager->id,
            ],
        );

        $rooms = collect([
            [
                'number' => '101',
                'capacity' => 2,
                'price' => 15000,
                'floor_id' => $floorOne->id,
                'manager_id' => $manager->id,
            ],
            [
                'number' => '102',
                'capacity' => 3,
                'price' => 18500,
                'floor_id' => $floorOne->id,
                'manager_id' => $manager->id,
            ],
            [
                'number' => '201',
                'capacity' => 2,
                'price' => 21000,
                'floor_id' => $floorTwo->id,
                'manager_id' => $manager->id,
            ],
            [
                'number' => '202',
                'capacity' => 4,
                'price' => 27500,
                'floor_id' => $floorTwo->id,
                'manager_id' => $manager->id,
            ],
        ])->map(function (array $attributes) {
            return Room::updateOrCreate(
                ['number' => $attributes['number']],
                $attributes,
            );
        });

        return $rooms->values();
    }

    protected function createClient(array $attributes): User
    {
        $user = User::updateOrCreate(
            ['email' => $attributes['email']],
            [
                'name' => $attributes['name'],
                'password' => Hash::make('123456'),
                'national_id' => $attributes['national_id'],
                'is_approved' => $attributes['is_approved'],
                'approved_by' => $attributes['approved_by'],
            ],
        );

        if (! $user->hasRole('client')) {
            $user->assignRole('client');
        }

        Client::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'email' => $user->email,
                'country' => $attributes['country'],
                'gender' => $attributes['gender'],
                'is_approved' => $attributes['is_approved'],
                'approved_by' => $attributes['approved_by'],
            ],
        );

        return $user->fresh('clientProfile');
    }
}
