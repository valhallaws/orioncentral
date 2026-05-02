@props([
    'title',
    'id' => uniqid(),
    'open' => false,
])

<div
    x-data="{ open: {{ $open ? 'true' : 'false' }} }"
    class="border-b border-secondary-200 dark:border-secondary-800 last:border-0"
>
    <h3>
        <button
            @click="open = !open"
            type="button"
            class="flex items-center justify-between w-full p-4 font-medium text-left text-secondary-900 bg-white dark:bg-secondary-900 dark:text-neutral-100 hover:bg-secondary-50 dark:hover:bg-secondary-800/50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset"
            :aria-expanded="open"
            aria-controls="accordion-body-{{ $id }}"
        >
            <span>{{ $title }}</span>
            <svg
                :class="{'rotate-180': open, 'rotate-0': !open }"
                class="w-5 h-5 transition-transform duration-200 shrink-0 text-secondary-500 dark:text-neutral-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    </h3>
    <div
        id="accordion-body-{{ $id }}"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="p-4 bg-white dark:bg-secondary-900 border-t border-secondary-200 dark:border-secondary-800 text-sm text-secondary-600 dark:text-neutral-400"
        style="display: none;"
    >
        {{ $slot }}
    </div>
</div>
