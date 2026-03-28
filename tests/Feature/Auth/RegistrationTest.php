<?php

namespace Tests\Feature\Auth;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('client', 'web');
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Register')
            ->has('countries')
        );
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'country' => 'Egypt',
            'gender' => 'male',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pending-approval', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('clients', [
            'email' => 'test@example.com',
            'country' => 'Egypt',
            'gender' => 'male',
            'is_approved' => false,
        ]);

        $this->assertNotNull(Client::where('email', 'test@example.com')->first()?->user_id);
    }
}
