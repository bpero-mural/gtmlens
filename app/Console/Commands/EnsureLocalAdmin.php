<?php

namespace App\Console\Commands;

use App\Support\LocalAdmin;
use Illuminate\Console\Command;

class EnsureLocalAdmin extends Command
{
    protected $signature = 'mural:ensure-local-admin';

    protected $description = 'Create or reset the local development admin user.';

    public function handle(): int
    {
        if (! app()->environment('local', 'testing')) {
            $this->components->warn('Skipped: local admin reset only runs in local/testing environments.');

            return self::SUCCESS;
        }

        $user = LocalAdmin::ensure();

        $this->components->info("Local admin is ready: {$user->email}");

        return self::SUCCESS;
    }
}
