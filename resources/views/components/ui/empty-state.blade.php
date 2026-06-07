@props([
    'title' => 'Nothing here yet',
    'message' => null,
])

<x-ui.card class="flex min-h-72 items-center justify-center text-center">
    <div class="max-w-md">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg border border-slate-200 bg-slate-50">
            <span class="h-5 w-5 rounded-sm border border-blue-300 bg-blue-50"></span>
        </div>
        <h2 class="text-lg font-semibold text-slate-950">{{ $title }}</h2>
        @if ($message)
            <p class="mt-2 text-sm text-slate-600">{{ $message }}</p>
        @endif
    </div>
</x-ui.card>
