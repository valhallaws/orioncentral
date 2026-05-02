@props([
    'variant' => 'primary',
    'type' => 'button',
    'size' => 'sm',
])

@php
    $baseClasses = 'inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none dark:focus:ring-offset-secondary-900';

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm',
    };

    $variantClasses = match($variant) {
        'primary' => 'bg-primary-500 text-white hover:bg-primary-600 focus:ring-primary-500',
        'secondary' => 'bg-secondary-200 text-secondary-900 hover:bg-secondary-300 focus:ring-secondary-500 dark:bg-secondary-800 dark:text-neutral-100 dark:hover:bg-secondary-700',
        'danger' => 'bg-danger-500 text-white hover:bg-danger-600 focus:ring-danger-500',
        'warning' => 'bg-warning-500 text-white hover:bg-warning-600 focus:ring-warning-500',
        'info' => 'bg-info-500 text-white hover:bg-info-600 focus:ring-info-500',
        'ghost' => 'bg-transparent text-secondary-700 hover:bg-secondary-100 focus:ring-secondary-500 dark:text-neutral-300 dark:hover:bg-secondary-800',
        default => 'bg-primary-500 text-white hover:bg-primary-600 focus:ring-primary-500',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "$baseClasses $sizeClasses $variantClasses"]) }}>
    {{ $slot }}
</button>
