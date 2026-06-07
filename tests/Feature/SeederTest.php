<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_local_admin_user(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@example.test')->first();

        $this->assertNotNull($admin);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_database_seeder_repairs_existing_local_admin_password(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('wrong-password'),
            'role' => 'viewer',
        ]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@example.test')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('password', $admin->password));
        $this->assertFalse(Hash::check('wrong-password', $admin->password));
    }

    public function test_local_admin_command_repairs_existing_local_admin_password(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('wrong-password'),
            'role' => 'viewer',
        ]);

        $this->artisan('gtm:ensure-local-admin')
            ->assertSuccessful();

        $admin = User::query()->where('email', 'admin@example.test')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('password', $admin->password));
    }
}
