@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'amis-card']) }}>
    @if ($title || $subtitle)
        <div class="border-b border-gray-200 px-4 py-4 sm:px-6 dark:border-gray-700">
            @if ($title)
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
            @endif
            @if ($subtitle)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    <div class="p-4 sm:p-6">
        {{ $slot }}
    </div>
</div>
