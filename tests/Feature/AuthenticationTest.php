<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('GTM Lens')
            ->assertSee('Sign in');
    }

    public function test_users_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->withSession(['_token' => 'test-token'])->post('/login', [
            '_token' => 'test-token',
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_local_admin_login_repairs_missing_admin_before_authentication(): void
    {
        $this->assertFalse(User::query()->where('email', 'admin@example.test')->exists());

        $this->withSession(['_token' => 'test-token'])->post('/login', [
            '_token' => 'test-token',
            'email' => 'admin@example.test',
            'password' => 'password',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
        $this->assertTrue(User::query()->where('email', 'admin@example.test')->exists());
    }

    public function test_local_admin_login_repairs_wrong_password_hash_before_authentication(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'wrong-password',
            'role' => 'viewer',
        ]);

        $this->withSession(['_token' => 'test-token'])->post('/login', [
            '_token' => 'test-token',
            'email' => 'admin@example.test',
            'password' => 'password',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $this->withSession(['_token' => 'test-token'])->post('/login', [
            '_token' => 'test-token',
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
