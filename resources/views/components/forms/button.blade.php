@props(['type' => 'submit', 'variant' => 'primary'])

@php
    $classes = match($variant) {
        'primary' => 'bg-primary hover:bg-secondary text-white',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-800',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        default => 'bg-primary hover:bg-secondary text-white',
    };
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors {$classes}"]) }}
>
    {{ $slot }}
</button>
