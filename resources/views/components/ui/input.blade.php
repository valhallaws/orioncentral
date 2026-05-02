@props([
    'disabled' => false,
    'type' => 'text',
    'id' => null,
    'name' => null,
    'label' => null,
    'error' => null,
    'required' => false
])

@php
    // If id is not provided, fallback to name
    $id = $id ?? $name;

    // Use Livewire's validation error if error prop is not set but name is
    if ($name && !isset($error)) {
        $error = $errors->first($name);
    }
@endphp

<div class="space-y-1">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-secondary-700 dark:text-neutral-300">
            {{ $label }} @if($required) <span class="text-danger-500">*</span> @endif
        </label>
    @endif

    <input
        {{ $disabled ? 'disabled' : '' }}
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        {!! $attributes->merge([
            'class' => 'block w-full px-3 py-1 rounded-md text-secondary-900 bg-white border border-secondary-300 placeholder-secondary-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/50 sm:text-sm transition-colors duration-200 disabled:opacity-50 disabled:bg-secondary-50 dark:bg-secondary-900 dark:border-secondary-700 dark:text-neutral-100 dark:placeholder-secondary-500 dark:focus:ring-primary-500/50 dark:disabled:bg-secondary-800'
        ]) !!}
    >

    @if ($error)
        <p class="text-sm text-danger-500 dark:text-danger-400 mt-1">
            {{ $error }}
        </p>
    @endif
</div>
