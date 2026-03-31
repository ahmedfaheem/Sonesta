<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class AnalyticsChartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = $this->createManager();
        $rooms = $this->ensureRooms($manager);
        $approverId = User::query()->role('receptionist')->value('id');

        $clients = collect([
            [
                'name' => 'Lina Hassan',
                'email' => 'lina.hassan.analytics@test.com',
                'national_id' => '94000000000001',
                'country' => 'Egypt',
                'gender' => 'female',
                'reservations' => 8,
            ],
            [
                'name' => 'Omar Khaled',
                'email' => 'omar.khaled.analytics@test.com',
                'national_id' => '94000000000002',
                'country' => 'United Arab Emirates',
                'gender' => 'male',
                'reservations' => 6,
            ],
            [
                'name' => 'Sofia Bianchi',
                'email' => 'sofia.bianchi.analytics@test.com',
                'national_id' => '94000000000003',
                'country' => 'Italy',
                'gender' => 'female',
                'reservations' => 5,
            ],
            [
                'name' => 'Noah Martinez',
                'email' => 'noah.martinez.analytics@test.com',
                'national_id' => '94000000000004',
                'country' => 'Spain',
                'gender' => 'male',
                'reservations' => 4,
            ],
            [
                'name' => 'Maya Dubois',
                'email' => 'maya.dubois.analytics@test.com',
                'national_id' => '94000000000005',
                'country' => 'France',
                'gender' => 'female',
                'reservations' => 3,
            ],
            [
                'name' => 'Adam Nasser',
                'email' => 'adam.nasser.analytics@test.com',
                'national_id' => '94000000000006',
                'country' => 'Jordan',
                'gender' => 'male',
                'reservations' => 2,
            ],
            [
                'name' => 'Sara Adel',
                'email' => 'sara.adel.analytics@test.com',
                'national_id' => '94000000000007',
                'country' => 'Egypt',
                'gender' => 'female',
                'reservations' => 2,
            ],
        ])->map(function (array $attributes) use ($approverId) {
            $user = User::updateOrCreate(
                ['email' => $attributes['email']],
                [
                    'name' => $attributes['name'],
                    'password' => Hash::make('123456'),
                    'national_id' => $attributes['national_id'],
                    'is_approved' => true,
                    'approved_by' => $approverId,
                ],
            );

            if (! $user->hasRole('client')) {
                $user->assignRole('client');
            }

            $client = Client::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $attributes['name'],
                    'email' => $attributes['email'],
                    'country' => $attributes['country'],
                    'gender' => $attributes['gender'],
                    'is_approved' => true,
                    'approved_by' => $approverId,
                ],
            );

            return [
                'client' => $client,
                'reservations' => $attributes['reservations'],
            ];
        });

        $this->seedReservations($clients, $rooms);
    }

    protected function createManager(): User
    {
        $manager = User::updateOrCreate(
            ['email' => 'analytics.manager@test.com'],
            [
                'name' => 'Analytics Manager',
                'password' => Hash::make('123456'),
                'national_id' => '94000000009999',
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
        $floor = Floor::updateOrCreate(
            ['number' => '3001'],
            [
                'name' => 'Analytics Floor',
                'manager_id' => $manager->id,
            ],
        );

        return collect([
            [
                'number' => '3101',
                'capacity' => 2,
                'price' => 14000,
                'floor_id' => $floor->id,
                'manager_id' => $manager->id,
            ],
            [
                'number' => '3102',
                'capacity' => 3,
                'price' => 18500,
                'floor_id' => $floor->id,
                'manager_id' => $manager->id,
            ],
            [
                'number' => '3103',
                'capacity' => 4,
                'price' => 22500,
                'floor_id' => $floor->id,
                'manager_id' => $manager->id,
            ],
            [
                'number' => '3104',
                'capacity' => 2,
                'price' => 17000,
                'floor_id' => $floor->id,
                'manager_id' => $manager->id,
            ],
        ])->map(function (array $attributes) {
            return Room::updateOrCreate(
                ['number' => $attributes['number']],
                $attributes,
            );
        })->values();
    }

    protected function seedReservations(Collection $clients, Collection $rooms): void
    {
        $baseMonth = now()->startOfMonth();

        $clients->each(function (array $record, int $clientIndex) use ($rooms, $baseMonth): void {
            /** @var Client $client */
            $client = $record['client'];
            $reservationsCount = $record['reservations'];

            for ($i = 0; $i < $reservationsCount; $i++) {
                /** @var Room $room */
                $room = $rooms[($clientIndex + $i) % $rooms->count()];
                $reservationDate = $baseMonth
                    ->copy()
                    ->subMonths(($i + $clientIndex) % 8)
                    ->addDays(3 + (($clientIndex * 2 + $i) % 20))
                    ->setTime(12, 0);

                Reservation::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'room_id' => $room->id,
                        'reservation_date' => $reservationDate,
                    ],
                    [
                        'accompany_number' => min($room->capacity, 1 + (($i + $clientIndex) % $room->capacity)),
                        'paid_price' => $room->price + (($clientIndex + 1) * 1200) + ($i * 450),
                    ],
                );
            }
        });
    }
}