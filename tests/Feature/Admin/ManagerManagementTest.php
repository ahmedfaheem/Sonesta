<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManagerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('manager', 'web');
    }

    public function test_admin_can_view_managers_index_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.managers.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Managers/Index')
            ->has('managers.data', 1)
            ->where('managers.data.0.email', $manager->email)
        );
    }

    public function test_admin_can_create_a_manager(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = Str::random(40);

        $response = $this
            ->actingAs($admin)
            ->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->post(route('admin.managers.store'), [
                '_token' => $token,
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'password' => 'secret123',
                'national_id' => '1234567890',
                'avatar' => UploadedFile::fake()->image('manager.jpg'),
            ]);

        $response->assertRedirect(route('admin.managers.index'));
        $response->assertSessionHas('success', 'Manager created');

        $manager = User::where('email', 'manager@example.com')->first();

        $this->assertNotNull($manager);
        $this->assertTrue($manager->hasRole('manager'));
        $this->assertSame($admin->id, $manager->created_by);
        $this->assertTrue(Hash::check('secret123', $manager->password));
        $this->assertNotNull($manager->avatar);
        Storage::disk('public')->assertExists($manager->avatar);
    }

    public function test_admin_can_update_a_manager_and_keep_password_if_not_provided(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = Str::random(40);

        $manager = User::factory()->create([
            'password' => Hash::make('old-password'),
            'national_id' => '1111111111',
            'avatar' => UploadedFile::fake()->image('old.jpg')->store('avatars', 'public'),
        ]);
        $manager->assignRole('manager');

        $response = $this
            ->actingAs($admin)
            ->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->put(route('admin.managers.update', $manager), [
                '_token' => $token,
                'name' => 'Updated Manager',
                'email' => 'updated-manager@example.com',
                'password' => '',
                'national_id' => '2222222222',
                'avatar' => UploadedFile::fake()->image('new.jpg'),
            ]);

        $response->assertRedirect(route('admin.managers.index'));
        $response->assertSessionHas('success', 'Manager updated');

        $manager->refresh();

        $this->assertSame('Updated Manager', $manager->name);
        $this->assertSame('updated-manager@example.com', $manager->email);
        $this->assertSame('2222222222', $manager->national_id);
        $this->assertTrue(Hash::check('old-password', $manager->password));
        Storage::disk('public')->assertExists($manager->avatar);
    }

    public function test_admin_can_delete_a_manager(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = Str::random(40);

        $manager = User::factory()->create([
            'avatar' => UploadedFile::fake()->image('manager.jpg')->store('avatars', 'public'),
        ]);
        $manager->assignRole('manager');

        $avatar = $manager->avatar;

        $response = $this
            ->actingAs($admin)
            ->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->delete(route('admin.managers.destroy', $manager), [
                '_token' => $token,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Deleted');
        $this->assertDatabaseMissing('users', ['id' => $manager->id]);
        Storage::disk('public')->assertMissing($avatar);
    }
}
