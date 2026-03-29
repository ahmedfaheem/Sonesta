<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ==============================
        // 1. CREATE PERMISSIONS
        // ==============================

        $permissions = [

            'view dashboard',

            // Managers
            'view managers',
            'create managers',
            'update managers',
            'delete managers',

            // Receptionists
            'view receptionists',
            'create receptionists',
            'update receptionists',
            'delete receptionists',
            'ban receptionists',

            // Clients
            'view clients',
            'create clients',
            'update clients',
            'delete clients',
            'approve clients',

            // Floors
            'view floors',
            'create floors',
            'update floors',
            'delete floors',

            // Rooms
            'view rooms',
            'create rooms',
            'update rooms',
            'delete rooms',

            // Reservations
            'view reservations',
            'create reservations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ==============================
        // 2. CREATE ROLES
        // ==============================

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $receptionist = Role::firstOrCreate(['name' => 'receptionist']);
        $client = Role::firstOrCreate(['name' => 'client']);

        // ==============================
        // 3. ASSIGN PERMISSIONS
        // ==============================

        // Admin → ALL permissions
        $admin->syncPermissions(Permission::all());

        // Manager
        $manager->syncPermissions([
            'view dashboard',

            'view receptionists',
            'create receptionists',
            'update receptionists',
            'delete receptionists',
            'ban receptionists',

            'view floors',
            'create floors',
            'update floors',
            'delete floors',

            'view rooms',
            'create rooms',
            'update rooms',
            'delete rooms',
        ]);

        // Receptionist
        $receptionist->syncPermissions([
            'view dashboard',

            'view clients',
            'approve clients',

            'view reservations',
        ]);

        // Client
        $client->syncPermissions([
            'view rooms',
            'create reservations',
        ]);
    }
}
