<x-layouts.guest>
    <div class="w-full max-w-md">
        <div class="mb-6">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-sm font-bold text-blue-700">ML</span>
                <div>
                    <p class="text-base font-semibold">Mural Lens</p>
                    <p class="text-xs text-slate-500">Local access</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h1 class="text-2xl font-semibold">Sign in</h1>
        </div>

        <x-ui.card>
            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <x-ui.input
                    label="Email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                    autofocus
                />

                <x-ui.input
                    label="Password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                />

                <x-ui.checkbox name="remember" label="Keep me signed in" />

                <x-ui.button type="submit" class="w-full justify-center">Sign in</x-ui.button>
            </form>
        </x-ui.card>
    </div>
</x-layouts.guest>
