<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReceptionistTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = $this->createManager();

        $receptionists = collect([
            [
                'name' => 'Nour Hassan',
                'email' => 'nour.hassan@test.com',
                'national_id' => '90000000000001',
            ],
            [
                'name' => 'Layla Farouk',
                'email' => 'layla.farouk@test.com',
                'national_id' => '90000000000002',
            ],
        ])->map(fn (array $attributes) => $this->createReceptionist($attributes, $manager));

        $rooms = $this->ensureRooms($manager);

        collect([
            [
                'name' => 'Omar Abdelrahman',
                'email' => 'omar.abdelrahman@test.com',
                'national_id' => '91000000000001',
                'country' => 'Egypt',
                'gender' => 'male',
                'is_approved' => false,
                'approved_by' => null,
            ],
            [
                'name' => 'Mariam Adel',
                'email' => 'mariam.adel@test.com',
                'national_id' => '91000000000002',
                'country' => 'France',
                'gender' => 'female',
                'is_approved' => false,
                'approved_by' => null,
            ],
            [
                'name' => 'Youssef Kamal',
                'email' => 'youssef.kamal@test.com',
                'national_id' => '91000000000003',
                'country' => 'Jordan',
                'gender' => 'male',
                'is_approved' => false,
                'approved_by' => null,
            ],
            [
                'name' => 'Salma Nabil',
                'email' => 'salma.nabil@test.com',
                'national_id' => '91000000000004',
                'country' => 'Italy',
                'gender' => 'female',
                'is_approved' => false,
                'approved_by' => null,
            ],
            [
                'name' => 'Khaled Mostafa',
                'email' => 'khaled.mostafa@test.com',
                'national_id' => '91000000000005',
                'country' => 'Spain',
                'gender' => 'male',
                'is_approved' => false,
                'approved_by' => null,
            ],
        ])->each(fn (array $attributes) => $this->createClient($attributes));

        $approvedClients = collect([
            [
                'name' => 'Ahmed Samir',
                'email' => 'ahmed.samir@test.com',
                'national_id' => '92000000000001',
                'country' => 'Egypt',
                'gender' => 'male',
                'is_approved' => true,
                'approved_by' => $receptionists[0]->id,
            ],
            [
                'name' => 'Giulia Rossi',
                'email' => 'giulia.rossi@test.com',
                'national_id' => '92000000000002',
                'country' => 'Italy',
                'gender' => 'female',
                'is_approved' => true,
                'approved_by' => $receptionists[0]->id,
            ],
            [
                'name' => 'Carlos Ortega',
                'email' => 'carlos.ortega@test.com',
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

            // Ensure each approved client has exactly one reservation and no shared room/date slot.
            $room = $rooms[$index % $rooms->count()];

            Reservation::updateOrCreate(
                [
                    'client_id' => $client->id,
                    'room_id' => $room->id,
                    'reservation_date' => now()->subDays($index + 1),
                ],
                [
                    'accompany_number' => min($index + 1, $room->capacity),
                    'paid_price' => $room->price + (($index + 1) * 2000),
                ],
            );
        });
    }

    protected function createReceptionist(array $attributes, User $manager): User
    {
        $user = User::updateOrCreate(
            ['email' => $attributes['email']],
            [
                'name' => $attributes['name'],
                'password' => Hash::make('123456'),
                'national_id' => $attributes['national_id'],
                'created_by' => $manager->id,
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
        $manager = User::updateOrCreate(
            ['email' => 'manager.seed@test.com'],
            [
                'name' => 'Mahmoud Elsayed',
                'password' => Hash::make('123456'),
                'national_id' => '93000000000001',
                'is_approved' => true,
                'approved_by' => null,
            ],
        );

        if (! $manager->hasRole('manager')) {
            $manager->assignRole('manager');
        }

        return $manager;
    }

    protected function ensureRooms(User $manager): Collection
    {
        $floorOne = Floor::firstOrCreate(
            ['number' => '1001'],
            [
                'name' => 'Nile Wing - First Floor',
                'manager_id' => $manager->id,
            ],
        );

        $floorTwo = Floor::firstOrCreate(
            ['number' => '1002'],
            [
                'name' => 'Nile Wing - Second Floor',
                'manager_id' => $manager->id,
            ],
        );

        $rooms = collect([
            [
                'number' => '1101',
                'capacity' => 2,
                'price' => 15000,
                'floor_id' => $floorOne->id,
                'manager_id' => $manager->id,
            ],
            [
                'number' => '1102',
                'capacity' => 3,
                'price' => 18500,
                'floor_id' => $floorOne->id,
                'manager_id' => $manager->id,
            ],
            [
                'number' => '1201',
                'capacity' => 2,
                'price' => 21000,
                'floor_id' => $floorTwo->id,
                'manager_id' => $manager->id,
            ],
            [
                'number' => '1202',
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
