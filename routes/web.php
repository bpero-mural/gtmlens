<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\MetadataEntity;
use App\Models\SalesforceOrg;
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

    Route::get('/search', function () {
        $search = trim((string) request('q', ''));
        $type = trim((string) request('type', ''));
        $status = trim((string) request('status', ''));

        $query = MetadataEntity::query()
            ->with(['salesforceOrg', 'searchDocument'])
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%'.strtolower(str_replace(['%', '_'], ['\\%', '\\_'], $search)).'%';

                $query->where(function ($query) use ($like): void {
                    $query->whereRaw('lower(api_name) like ?', [$like])
                        ->orWhereRaw('lower(label) like ?', [$like])
                        ->orWhereRaw('lower(external_key) like ?', [$like])
                        ->orWhereHas('searchDocument', fn ($query) => $query->whereRaw('lower(content) like ?', [$like]));
                });
            })
            ->when($type !== '', fn ($query) => $query->where('metadata_type', $type))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('metadata_type')
            ->orderBy('api_name');

        return view('pages.search', [
            'entities' => $query->paginate(25)->withQueryString(),
            'types' => MetadataEntity::query()->select('metadata_type')->distinct()->orderBy('metadata_type')->pluck('metadata_type'),
            'statuses' => MetadataEntity::query()->select('status')->distinct()->orderBy('status')->pluck('status'),
            'filters' => [
                'q' => $search,
                'type' => $type,
                'status' => $status,
            ],
        ]);
    })->name('search.index');

    Route::get('/metadata/{metadataEntity}', function (MetadataEntity $metadataEntity) {
        $metadataEntity->load(['salesforceOrg', 'versions' => fn ($query) => $query->latest('version_number'), 'searchDocument']);

        return view('pages.metadata.show', [
            'entity' => $metadataEntity,
            'latestVersion' => $metadataEntity->versions->first(),
        ]);
    })->name('metadata.show');

    Route::view('/dictionary', 'pages.dictionary.index')->name('dictionary.index');
    Route::view('/timeline', 'pages.timeline.index')->name('timeline.index');
    Route::view('/issues', 'pages.issues.index')->name('issues.index');
    Route::view('/sync-runs', 'pages.sync-runs.index')->name('sync-runs.index');


    Route::view('/admin', 'pages.admin.index')->name('admin.index');
});
