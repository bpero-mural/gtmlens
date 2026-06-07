<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\SalesforceOrg;
use App\Models\SyncRun;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/salesforce-orgs', function () {
        return view('pages.salesforce-orgs', [
            'orgs' => SalesforceOrg::query()->latest()->get(),
        ]);
    })->name('salesforce-orgs.index');
    Route::view('/search', 'pages.search')->name('search.index');
    Route::view('/dictionary', 'pages.dictionary.index')->name('dictionary.index');
    Route::view('/timeline', 'pages.timeline.index')->name('timeline.index');
    Route::view('/issues', 'pages.issues.index')->name('issues.index');
    Route::get('/sync-runs', function () {
        return view('pages.sync-runs.index', [
            'syncRuns' => SyncRun::query()->with('salesforceOrg')->latest()->limit(50)->get(),
        ]);
    })->name('sync-runs.index');
    Route::view('/admin', 'pages.admin.index')->name('admin.index');
});
