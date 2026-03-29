<?php

namespace Tests\Feature\Receptionist;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('manager', 'web');
        Role::findOrCreate('receptionist', 'web');
        Role::findOrCreate('client', 'web');
    }

    public function test_receptionist_can_view_pending_clients(): void
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('receptionist');

        $pendingClient = $this->createClientUser([
            'name' => 'Pending Client',
            'email' => 'pending@example.com',
        ]);

        $approvedClient = $this->createClientUser([
            'name' => 'Approved Client',
            'email' => 'approved@example.com',
            'is_approved' => true,
        ]);

        $response = $this
            ->actingAs($receptionist)
            ->get(route('receptionist.clients.pending'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Receptionist/Clients/Pending')
            ->has('clients.data', 1)
            ->where('clients.data.0.id', $pendingClient->id)
            ->where('clients.data.0.email', $pendingClient->email)
        );

        $this->assertDatabaseHas('clients', [
            'user_id' => $approvedClient->id,
            'is_approved' => true,
        ]);
    }

    public function test_receptionist_can_approve_a_pending_client_and_record_the_approver(): void
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('receptionist');

        $client = $this->createClientUser();

        $response = $this
            ->actingAs($receptionist)
            ->patch(route('receptionist.clients.approve', $client));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $client->id,
            'is_approved' => true,
            'approved_by' => $receptionist->id,
        ]);

        $this->assertDatabaseHas('clients', [
            'user_id' => $client->id,
            'is_approved' => true,
            'approved_by' => $receptionist->id,
        ]);
    }

    public function test_my_approved_clients_page_is_scoped_to_the_logged_in_receptionist(): void
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('receptionist');

        $otherReceptionist = User::factory()->create();
        $otherReceptionist->assignRole('receptionist');

        $myClient = $this->createClientUser([
            'name' => 'My Client',
            'email' => 'mine@example.com',
            'is_approved' => true,
            'approved_by' => $receptionist->id,
        ]);

        $this->createClientUser([
            'name' => 'Other Client',
            'email' => 'other@example.com',
            'is_approved' => true,
            'approved_by' => $otherReceptionist->id,
        ]);

        $response = $this
            ->actingAs($receptionist)
            ->get(route('receptionist.clients.approved'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Receptionist/Clients/MyApproved')
            ->has('clients.data', 1)
            ->where('clients.data.0.id', $myClient->id)
            ->where('clients.data.0.email', $myClient->email)
        );
    }

    public function test_non_receptionists_cannot_access_receptionist_client_routes(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $client = $this->createClientUser();

        $this->actingAs($manager)
            ->get(route('receptionist.clients.pending'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->patch(route('receptionist.clients.approve', $client))
            ->assertForbidden();
    }

    protected function createClientUser(array $overrides = []): User
    {
        $userAttributes = array_filter([
            'name' => $overrides['name'] ?? null,
            'email' => $overrides['email'] ?? null,
            'is_approved' => $overrides['is_approved'] ?? false,
            'approved_by' => $overrides['approved_by'] ?? null,
        ], static fn (mixed $value) => $value !== null);

        $user = User::factory()->create($userAttributes);
        $user->assignRole('client');

        Client::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'country' => 'Egypt',
            'gender' => 'male',
            'is_approved' => $overrides['is_approved'] ?? false,
            'approved_by' => $overrides['approved_by'] ?? null,
        ]);

        return $user;
    }
}
