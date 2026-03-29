<?php

namespace Tests\Feature\Auth;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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
        $token = Str::random(40);

        $response = $this
            ->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->post('/register', [
                '_token' => $token,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'national_id' => '29801011234567',
                'country' => 'Egypt',
                'gender' => 'male',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('status', 'Your account is pending approval');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'national_id' => '29801011234567',
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
