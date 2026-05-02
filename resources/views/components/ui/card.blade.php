@props([
    'title' => null,
    'description' => null,
    'footer' => null,
    'noPadding' => false,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-secondary-900 rounded-xl shadow-sm border border-secondary-200 dark:border-secondary-800 overflow-hidden transition-colors duration-200']) }}>
    @if ($title || isset($header))
        <div class="px-6 py-4 border-b border-secondary-100 dark:border-secondary-800">
            @if(isset($header))
                {{ $header }}
            @else
                <h3 class="text-lg font-semibold text-secondary-900 dark:text-neutral-100">
                    {{ $title }}
                </h3>
                @if ($description)
                    <p class="mt-1 text-sm text-secondary-500 dark:text-neutral-400">
                        {{ $description }}
                    </p>
                @endif
            @endif
        </div>
    @endif

    <div class="{{ $noPadding ? '' : 'p-6' }}">
        {{ $slot }}
    </div>

    @if ($footer || isset($footerSlot))
        <div class="px-6 py-4 bg-secondary-50 dark:bg-secondary-950/50 border-t border-secondary-100 dark:border-secondary-800 text-right">
            {{ $footer ?? $footerSlot ?? '' }}
        </div>
    @endif
</div>
