@props([
    'variant' => 'info',
    'title' => null,
])

@php
    $baseClasses = 'p-4 mb-4 text-sm rounded-lg flex items-start border';

    $variantClasses = match($variant) {
        'primary' => 'text-primary-800 bg-primary-500 border-primary-200 dark:bg-secondary-800 dark:text-primary-400 dark:border-primary-800',
        'success' => 'text-success-800 bg-success-500 border-success-200 dark:bg-secondary-800 dark:text-success-400 dark:border-success-800',
        'danger' => 'text-danger-800 bg-danger-500 border-danger-200 dark:bg-secondary-800 dark:text-danger-400 dark:border-danger-800',
        'warning' => 'text-warning-800 bg-warning-500 border-warning-200 dark:bg-secondary-800 dark:text-warning-400 dark:border-warning-800',
        'info' => 'text-info-800 bg-info-500 border-info-200 dark:bg-secondary-800 dark:text-info-400 dark:border-info-800',
        default => 'text-secondary-800 bg-secondary-500 border-secondary-200 dark:bg-secondary-800 dark:text-secondary-300 dark:border-secondary-700',
    };
@endphp

<div {{ $attributes->merge(['class' => "$baseClasses $variantClasses"]) }} role="alert">
    <!-- Icon Placeholder based on variant if needed -->
    <div class="mr-3 mt-0.5 flex-shrink-0">
        @if ($variant === 'success')
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        @elseif ($variant === 'danger')
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
        @elseif ($variant === 'warning')
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
        @else
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
        @endif
    </div>

    <div>
        @if ($title)
            <span class="font-medium block mb-1">{{ $title }}</span>
        @endif
        <div class="text-sm">
            {{ $slot }}
        </div>
    </div>
</div>
