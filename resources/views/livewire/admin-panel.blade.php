<x-ui.card>
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Role</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Stage 0 access</th>
            </tr>
        </x-slot:head>

        @foreach ($roles as $role)
            <tr>
                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $role }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">Local authenticated shell</td>
            </tr>
        @endforeach
    </x-ui.table>
    </x-ui.card>
