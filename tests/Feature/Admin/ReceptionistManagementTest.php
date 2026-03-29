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

class ReceptionistManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('receptionist', 'web');
    }

    public function test_admin_can_view_receptionists_index_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $receptionist = User::factory()->create();
        $receptionist->assignRole('receptionist');

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.receptionists.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Receptionists/Index')
            ->has('receptionists.data', 1)
            ->where('receptionists.data.0.email', $receptionist->email)
        );
    }

    public function test_admin_can_create_a_receptionist(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = Str::random(40);

        $response = $this
            ->actingAs($admin)
            ->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->post(route('admin.receptionists.store'), [
                '_token' => $token,
                'name' => 'Receptionist User',
                'email' => 'receptionist@example.com',
                'password' => 'secret123',
                'national_id' => '9876543210',
                'avatar' => UploadedFile::fake()->image('receptionist.jpg'),
            ]);

        $response->assertRedirect(route('admin.receptionists.index'));
        $response->assertSessionHas('success', 'Receptionist created');

        $receptionist = User::where('email', 'receptionist@example.com')->first();

        $this->assertNotNull($receptionist);
        $this->assertTrue($receptionist->hasRole('receptionist'));
        $this->assertSame($admin->id, $receptionist->created_by);
        $this->assertTrue(Hash::check('secret123', $receptionist->password));
        $this->assertNotNull($receptionist->avatar);
        Storage::disk('public')->assertExists($receptionist->avatar);
    }

    public function test_admin_can_update_a_receptionist_and_keep_password_if_not_provided(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = Str::random(40);

        $receptionist = User::factory()->create([
            'password' => Hash::make('old-password'),
            'national_id' => '1231231231',
            'avatar' => UploadedFile::fake()->image('old.jpg')->store('avatars', 'public'),
        ]);
        $receptionist->assignRole('receptionist');

        $response = $this
            ->actingAs($admin)
            ->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->put(route('admin.receptionists.update', $receptionist), [
                '_token' => $token,
                'name' => 'Updated Receptionist',
                'email' => 'updated-receptionist@example.com',
                'password' => '',
                'national_id' => '3213213213',
                'avatar' => UploadedFile::fake()->image('new.jpg'),
            ]);

        $response->assertRedirect(route('admin.receptionists.index'));
        $response->assertSessionHas('success', 'Receptionist updated');

        $receptionist->refresh();

        $this->assertSame('Updated Receptionist', $receptionist->name);
        $this->assertSame('updated-receptionist@example.com', $receptionist->email);
        $this->assertSame('3213213213', $receptionist->national_id);
        $this->assertTrue(Hash::check('old-password', $receptionist->password));
        Storage::disk('public')->assertExists($receptionist->avatar);
    }

    public function test_admin_can_delete_a_receptionist(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = Str::random(40);

        $receptionist = User::factory()->create([
            'avatar' => UploadedFile::fake()->image('receptionist.jpg')->store('avatars', 'public'),
        ]);
        $receptionist->assignRole('receptionist');

        $avatar = $receptionist->avatar;

        $response = $this
            ->actingAs($admin)
            ->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->delete(route('admin.receptionists.destroy', $receptionist), [
                '_token' => $token,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Deleted');
        $this->assertDatabaseMissing('users', ['id' => $receptionist->id]);
        Storage::disk('public')->assertMissing($avatar);
    }
}
