@props(['title', 'subtitle' => null, 'chart'])

<section {{ $attributes->merge(['class' => 'dashboard-card']) }}>
    <div class="mb-5 flex items-start justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-950">{{ $title }}</h2>
            @if ($subtitle)
                <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
        <span class="rounded-full border border-emerald-100 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Live</span>
    </div>
    <div id="{{ $chart }}" class="chart-surface"></div>
</section>
