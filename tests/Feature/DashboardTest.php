<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_for_authenticated_users(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Livewire active')
            ->assertSee('Stage 0');
    }

    public function test_placeholder_pages_are_protected_and_render(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        foreach (['/salesforce-orgs', '/search', '/dictionary', '/timeline', '/issues', '/sync-runs', '/admin'] as $path) {
            $this->get($path)->assertRedirect('/login');
        }

        foreach (['/salesforce-orgs', '/search', '/dictionary', '/timeline', '/issues', '/sync-runs', '/admin'] as $path) {
            $this->actingAs($user)->get($path)->assertOk();
        }
    }
}
