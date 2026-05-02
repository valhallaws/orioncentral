@props([
    'variant' => 'info',
    'rounded' => false,
])

@php
    $baseClasses = 'inline-flex items-center px-2.5 py-0.5 text-xs font-medium';
    $roundedClasses = $rounded ? 'rounded-full' : 'rounded';

    $variantClasses = match($variant) {
        'primary' => 'bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400',
        'secondary' => 'bg-secondary-100 text-secondary-800 dark:bg-secondary-800 dark:text-secondary-300',
        'success' => 'bg-success-50 text-success-600 dark:bg-success-900/30 dark:text-success-400',
        'danger' => 'bg-danger-50 text-danger-600 dark:bg-danger-900/30 dark:text-danger-400',
        'warning' => 'bg-warning-50 text-warning-600 dark:bg-warning-900/30 dark:text-warning-400',
        'info' => 'bg-info-50 text-info-600 dark:bg-info-900/30 dark:text-info-400',
        default => 'bg-secondary-100 text-secondary-800 dark:bg-secondary-800 dark:text-secondary-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "$baseClasses $roundedClasses $variantClasses"]) }}>
    {{ $slot }}
</span>
