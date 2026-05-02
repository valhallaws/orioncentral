@props(['tabs' => []])

<div x-data="{ activeTab: '{{ count($tabs) > 0 ? $tabs[0]['id'] : '' }}' }" class="w-full">
    <!-- Tab Headers -->
    <div class="border-b border-secondary-200 dark:border-secondary-800">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            @foreach($tabs as $tab)
                <button
                    @click="activeTab = '{{ $tab['id'] }}'"
                    :class="{
                        'border-primary-500 text-primary-600 dark:text-primary-500': activeTab === '{{ $tab['id'] }}',
                        'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300 dark:text-neutral-400 dark:hover:text-neutral-300 dark:hover:border-secondary-600': activeTab !== '{{ $tab['id'] }}'
                    }"
                    class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                >
                    {{ $tab['label'] }}
                    @if(isset($tab['badge']))
                        <span
                            :class="activeTab === '{{ $tab['id'] }}' ? 'bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-secondary-100 text-secondary-800 dark:bg-secondary-800 dark:text-secondary-300'"
                            class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium"
                        >
                            {{ $tab['badge'] }}
                        </span>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
