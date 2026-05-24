@props(['href' => null, 'icon', 'name', 'owner', 'summary', 'accent' => 'emerald', 'shape' => 'soft', 'status' => null, 'disabled' => false])

@php
    $accentClass = 'module-accent-'.$accent;
    $shapeClass = 'module-shape-'.$shape;
@endphp

@if ($disabled)
<div class="module-owner-card {{ $accentClass }} {{ $shapeClass }} cursor-not-allowed opacity-70 grayscale-[20%]" aria-disabled="true">
@else
<a href="{{ $href }}" class="module-owner-card {{ $accentClass }} {{ $shapeClass }}">
@endif
    <span class="module-owner-icon">
        <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
    </span>
    <span class="min-w-0">
        <span class="flex items-center gap-2">
            <span class="block text-sm font-bold text-slate-950">{{ $name }}</span>
            @if ($status)
                <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[9px] font-black uppercase tracking-wider text-amber-700 ring-1 ring-amber-100">{{ $status }}</span>
            @endif
        </span>
        <span class="mt-1 block text-xs font-semibold text-emerald-700">Owned by {{ $owner }}</span>
        <span class="mt-1 block truncate text-xs text-slate-500">{{ $summary }}</span>
    </span>
@if ($disabled)
</div>
@else
</a>
@endif
