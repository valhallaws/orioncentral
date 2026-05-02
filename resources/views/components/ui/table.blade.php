@props([
    'headers' => [],
    'rows' => [],
])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full text-sm text-left text-secondary-500 dark:text-neutral-400']) }}>
        <thead class="text-xs text-secondary-700 uppercase bg-secondary-50 dark:bg-secondary-950/50 dark:text-neutral-400 border-b border-secondary-200 dark:border-secondary-800">
            <tr>
                @if(isset($headerSlot))
                    {{ $headerSlot }}
                @else
                    @foreach($headers as $header)
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ $header }}
                        </th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @if(isset($slot) && $slot->isNotEmpty())
                {{ $slot }}
            @else
                @forelse($rows as $row)
                    <tr class="bg-white border-b dark:bg-secondary-900 dark:border-secondary-800 hover:bg-secondary-50 dark:hover:bg-secondary-800/50 transition-colors duration-150">
                        @foreach($row as $cell)
                            <td class="px-6 py-4">
                                {{ $cell }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="px-6 py-4 text-center text-secondary-500 dark:text-neutral-400">
                            No hay datos disponibles.
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>
</div>
