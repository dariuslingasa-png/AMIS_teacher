@props([
    'src' => null,
    'alt' => '',
    'fallbackInitials' => null,
    'width' => 'w-10',
    'height' => 'h-10',
    'rounded' => 'rounded-md',
    'class' => '',
    'imgClass' => '',
    'containerClass' => '',
    'eager' => true,
])

@php
    $sizeClass = $width . ' ' . $height;
    $loadingAttr = $eager ? 'eager' : 'lazy';
    $decodingAttr = $eager ? 'async' : 'auto';
    $initials = $fallbackInitials ?: collect(explode(' ', $alt))->filter()->take(2)->map(fn ($p) => \Illuminate\Support\Str::substr($p, 0, 1))->join('');
@endphp

<div class="smart-image-wrap {{ $sizeClass }} {{ $rounded }} {{ $containerClass }}" {{ $attributes->except(['src', 'alt', 'fallbackInitials', 'width', 'height', 'rounded', 'class', 'imgClass', 'containerClass', 'eager']) }}>
    @if ($src)
        <div class="smart-image-skeleton {{ $sizeClass }} {{ $rounded }}"></div>
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            loading="{{ $loadingAttr }}"
            decoding="{{ $decodingAttr }}"
            class="smart-image {{ $sizeClass }} {{ $rounded }} {{ $imgClass }} {{ $class }}"
            onload="this.classList.add('smart-image-loaded');this.previousElementSibling.style.display='none'"
            onerror="this.style.display='none';this.previousElementSibling.style.display='none';this.nextElementSibling.style.display='flex'"
        >
        <span class="smart-image-fallback {{ $sizeClass }} {{ $rounded }}" style="display:none">
            {{ $initials ?: 'NA' }}
        </span>
    @else
        <span class="smart-image-fallback {{ $sizeClass }} {{ $rounded }}" style="display:flex">
            {{ $initials ?: 'NA' }}
        </span>
    @endif
</div>
