@props(['variant' => 'primary', 'type' => 'button'])

@php
    $classes = $variant === 'secondary'
        ? 'border border-gray-200 bg-white text-gray-900 hover:bg-gray-100 focus:ring-gray-100'
        : 'bg-primary-700 text-white hover:bg-primary-800 focus:ring-primary-300';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium focus:outline-none focus:ring-4 $classes"]) }}>
    {{ $slot }}
</button>
