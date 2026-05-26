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

<div class="shrink-0 overflow-hidden bg-gray-100 flex items-center justify-center {{ $rounded }} {{ $containerClass }}" style="width:{{ $px }}px;height:{{ $px }}px;">
    @if ($src)
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            class="w-full h-full object-cover block"
            width="{{ $px }}"
            height="{{ $px }}"
            loading="{{ $eager ? 'eager' : 'lazy' }}"
            decoding="{{ $eager ? 'async' : 'auto' }}"
            onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
        >
        <span class="w-full h-full items-center justify-center bg-slate-100 text-xs font-extrabold text-slate-600 uppercase" style="display:none">
            {{ $initials ?: 'NA' }}
        </span>
    @else
        <span class="w-full h-full flex items-center justify-center bg-slate-100 text-xs font-extrabold text-slate-600 uppercase">
            {{ $initials ?: 'NA' }}
        </span>
    @endif
</div>
