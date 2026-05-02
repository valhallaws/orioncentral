@props([
    'name' => null,
    'title' => null,
    'maxWidth' => '2xl'
])

@php
$maxWidthClass = match ($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
    default => 'sm:max-w-4xl',
};
@endphp

<div
    x-data="{
        show: @if($attributes->wire('model')->value()) @entangle($attributes->wire('model')) @else false @endif,
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                // Filter out disabled elements
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center"
    style="display: none;"
>
    <!-- Overlay -->
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-secondary-900/75 dark:bg-secondary-950/90 backdrop-blur-sm"></div>
    </div>

    <!-- Modal Content -->
    <div
        x-show="show"
        class="relative z-10 w-full mb-6 bg-white dark:bg-secondary-900 rounded-xl overflow-hidden shadow-xl transform transition-all {{ $maxWidthClass }} sm:mx-auto border border-secondary-200 dark:border-secondary-800 flex flex-col max-h-[90vh]"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        @click.stop
    >
        @if($title || isset($header))
            <div class="px-6 py-4 border-b border-secondary-100 dark:border-secondary-800 flex justify-between items-center bg-secondary-50/50 dark:bg-secondary-950/50">
                @if(isset($header))
                    {{ $header }}
                @else
                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-neutral-100">
                        {{ $title }}
                    </h3>
                @endif
                <button x-on:click="show = false" type="button" class="text-secondary-400 hover:text-secondary-600 dark:hover:text-neutral-300 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        <div class="px-6 py-4 overflow-y-auto">
            {{ $slot }}
        </div>

        @if(isset($footer))
            <div class="px-6 py-4 bg-secondary-50 dark:bg-secondary-950/50 border-t border-secondary-100 dark:border-secondary-800 text-right">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
