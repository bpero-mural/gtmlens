<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ isset($title) ? $title.' - ' : '' }}{{ config('app.name', 'Mural Lens') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-[#f5f7fb] text-slate-950 antialiased">
        <div class="flex min-h-screen">
            <aside class="hidden w-72 border-r border-slate-200 bg-white lg:block">
                <a href="{{ route('dashboard') }}" class="flex h-20 items-center gap-3 border-b border-slate-200 px-5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-sm font-bold text-blue-700">ML</span>
                    <span>
                        <span class="block text-base font-semibold">Mural Lens</span>
                        <span class="mt-0.5 block text-xs text-slate-500">Salesforce metadata control</span>
                    </span>
                </a>

                <nav class="px-3 py-5">
                    @php
                        $groups = [
                            'Foundation' => [
                                ['Dashboard', 'dashboard', 'Overview'],
                                ['Salesforce Orgs', 'salesforce-orgs.index', 'Connections'],
                                ['Sync Runs', 'sync-runs.index', 'Operations'],
                            ],
                            'Metadata' => [
                                ['Search', 'search.index', 'Quick find'],
                                ['Dictionary', 'dictionary.index', 'Definitions'],
                                ['Timeline', 'timeline.index', 'Changes'],
                                ['Issues', 'issues.index', 'Risks'],
                            ],
                            'System' => [
                                ['Admin', 'admin.index', 'Roles'],
                            ],
                        ];
                    @endphp

                    @foreach ($groups as $group => $links)
                        <div class="{{ $loop->first ? '' : 'mt-6' }}">
                            <p class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $group }}</p>
                            <div class="mt-2 space-y-1">
                                @foreach ($links as [$label, $route, $hint])
                                    <a
                                        href="{{ route($route) }}"
                                        class="flex items-center justify-between rounded-md px-3 py-2.5 text-sm transition {{ request()->routeIs($route) ? 'bg-blue-50 text-blue-800 ring-1 ring-blue-100' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-950' }}"
                                    >
                                        <span class="font-medium">{{ $label }}</span>
                                        <span class="text-xs {{ request()->routeIs($route) ? 'text-blue-600' : 'text-slate-400' }}">{{ $hint }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>

                <div class="mx-5 mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">MVP boundary</p>
                    <p class="mt-1 text-xs leading-5 text-amber-900">No Salesforce business record data. Metadata foundation only.</p>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="border-b border-slate-200 bg-white/95">
                    <div class="flex min-h-20 items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.badge tone="blue">Stage 0</x-ui.badge>
                                <x-ui.badge tone="teal">Docker local</x-ui.badge>
                            </div>
                            <h1 class="mt-2 truncate text-[22px] font-semibold leading-7 text-slate-950">{{ $title ?? 'Dashboard' }}</h1>
                        </div>

                        <div class="flex shrink-0 items-center gap-3">
                            <div class="hidden text-right sm:block">
                                <p class="text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ auth()->user()->role }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-ui.button type="submit" variant="secondary">Sign out</x-ui.button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="flex-1">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
