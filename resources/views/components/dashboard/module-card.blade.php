@props(['href', 'icon', 'name', 'owner', 'summary', 'accent' => 'emerald', 'shape' => 'soft'])

@php
    $accentClass = 'module-accent-'.$accent;
    $shapeClass = 'module-shape-'.$shape;
@endphp

<a href="{{ $href }}" class="module-owner-card {{ $accentClass }} {{ $shapeClass }}">
    <span class="module-owner-icon">
        <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
    </span>
    <span class="min-w-0">
        <span class="block text-sm font-bold text-slate-950">{{ $name }}</span>
        <span class="mt-1 block text-xs font-semibold text-emerald-700">Owned by {{ $owner }}</span>
        <span class="mt-1 block truncate text-xs text-slate-500">{{ $summary }}</span>
    </span>
</a>
