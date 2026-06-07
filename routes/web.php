<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
    Route::view('/salesforce-orgs', 'pages.salesforce-orgs')->name('salesforce-orgs.index');
    Route::view('/search', 'pages.search')->name('search.index');
    Route::view('/dictionary', 'pages.dictionary.index')->name('dictionary.index');
    Route::view('/timeline', 'pages.timeline.index')->name('timeline.index');
    Route::view('/issues', 'pages.issues.index')->name('issues.index');
    Route::view('/sync-runs', 'pages.sync-runs.index')->name('sync-runs.index');
    Route::view('/admin', 'pages.admin.index')->name('admin.index');
});
