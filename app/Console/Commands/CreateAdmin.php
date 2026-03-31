<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateAdmin extends Command
{
    protected $signature = 'create:admin
        {--name= : Admin name}
        {--email= : Admin email}
        {--password= : Admin password}';

    protected $description = 'Create an admin user account';

    public function handle(): int
    {
        $data = [
            'name' => (string) ($this->option('name') ?? ''),
            'email' => (string) ($this->option('email') ?? ''),
            'password' => (string) ($this->option('password') ?? ''),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $admin = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_approved' => true,
        ]);

        $admin->assignRole('admin');

        $this->info("Admin created: {$admin->email} (id: {$admin->id})");

        return self::SUCCESS;
    }
}

