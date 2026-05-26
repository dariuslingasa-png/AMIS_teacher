@props([
    'src' => null,
    'alt' => '',
    'class' => '',
    'containerClass' => '',
    'eager' => true,
])

@php
    $loadingAttr = $eager ? 'eager' : 'lazy';
    $decodingAttr = $eager ? 'async' : 'auto';
@endphp

@if ($src)
    <div class="smart-preview-wrap {{ $containerClass }}">
        <div class="smart-preview-skeleton"></div>
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            loading="{{ $loadingAttr }}"
            decoding="{{ $decodingAttr }}"
            class="smart-preview-img {{ $class }}"
            onload="this.classList.add('smart-image-loaded');this.previousElementSibling.style.display='none'"
            onerror="this.style.display='none';this.previousElementSibling.style.display='none';this.nextElementSibling.style.display='flex'"
        >
        <span class="smart-preview-fallback" style="display:none">
            <i data-lucide="image-off" class="h-8 w-8"></i>
            <span>Image unavailable</span>
        </span>
    </div>
@endif
