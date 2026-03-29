<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('client', 'web');
    }

    public function test_admin_can_view_clients_index_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $client = User::factory()->create();
        $client->assignRole('client');

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.clients.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Clients/Index')
            ->has('clients.data', 1)
            ->where('clients.data.0.email', $client->email)
        );
    }

    public function test_admin_can_view_client_details_page(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $client = User::factory()->create([
            'national_id' => '12345678901234',
            'avatar' => UploadedFile::fake()->image('client.jpg')->store('avatars', 'public'),
        ]);
        $client->assignRole('client');

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.clients.show', $client));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Clients/Show')
            ->where('client.id', $client->id)
            ->where('client.email', $client->email)
            ->where('client.national_id', '12345678901234')
            ->where('client.avatar_url', asset('storage/'.$client->avatar))
        );
    }

    public function test_admin_can_delete_a_client(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = Str::random(40);

        $client = User::factory()->create();
        $client->assignRole('client');

        $response = $this
            ->actingAs($admin)
            ->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->delete(route('admin.clients.destroy', $client), [
                '_token' => $token,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Deleted');
        $this->assertDatabaseMissing('users', ['id' => $client->id]);
    }
}
