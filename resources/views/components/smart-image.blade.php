@props([
    'src' => null,
    'alt' => '',
    'fallbackInitials' => null,
    'size' => '40',
    'rounded' => 'rounded-lg',
    'containerClass' => '',
    'eager' => true,
])

@php
    $px = (int) $size;
    $initials = $fallbackInitials ?: collect(explode(' ', $alt))->filter()->take(2)->map(fn ($p) => \Illuminate\Support\Str::substr($p, 0, 1))->join('');
@endphp

@if ($src)
    <div class="shrink-0 overflow-hidden bg-gray-100 flex items-center justify-center {{ $rounded }} {{ $containerClass }}" style="width:{{ $px }}px;height:{{ $px }}px;">
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="w-full h-full object-cover block"
            width="{{ $px }}"
            height="{{ $px }}"
            loading="{{ $eager ? 'eager' : 'lazy' }}"
            decoding="{{ $eager ? 'async' : 'auto' }}"
        >
    </div>
@else
    <div class="shrink-0 overflow-hidden bg-slate-100 flex items-center justify-center border border-slate-200 {{ $rounded }} {{ $containerClass }}" style="width:{{ $px }}px;height:{{ $px }}px;">
        <span class="text-xs font-extrabold text-slate-600 uppercase">{{ $initials ?: 'NA' }}</span>
    </div>
@endif
