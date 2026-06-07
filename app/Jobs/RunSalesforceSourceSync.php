<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class RunSalesforceSourceSync implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 900;

    public function __construct(public readonly string $orgAlias)
    {
        $this->onQueue('salesforce-sync');
    }

    public function handle(): void
    {
        $retrieveStatus = Artisan::call('salesforce:source-retrieve', [
            'orgAlias' => $this->orgAlias,
        ]);

        if ($retrieveStatus !== 0) {
            return;
        }

        Artisan::call('salesforce:source-normalize', [
            '--latest' => true,
            '--orgAlias' => $this->orgAlias,
        ]);
    }
}
