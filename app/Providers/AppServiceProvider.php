<?php

namespace App\Providers;

use App\Domain\Salesforce\Clients\SfCliClient;
use App\Domain\Salesforce\Clients\SfCliSourceRetriever;
use App\Domain\Salesforce\Contracts\SalesforceCliClient;
use App\Domain\Salesforce\Contracts\SalesforceSourceRetriever;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SalesforceCliClient::class, SfCliClient::class);
        $this->app->bind(SalesforceSourceRetriever::class, SfCliSourceRetriever::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
