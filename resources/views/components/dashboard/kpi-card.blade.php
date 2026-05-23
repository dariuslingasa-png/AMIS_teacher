@props(['metric'])

@php
    $trend = (float) ($metric['trend'] ?? 0);
    $isPositive = $trend >= 0;
@endphp

<article class="dashboard-card kpi-card">
    <div class="flex items-start justify-between gap-4">
        <div class="kpi-icon">
            <i data-lucide="{{ $metric['icon'] ?? 'activity' }}" class="h-5 w-5"></i>
        </div>
        <span class="trend-pill {{ $isPositive ? 'trend-pill-up' : 'trend-pill-down' }}">
            <i data-lucide="{{ $isPositive ? 'trending-up' : 'trending-down' }}" class="h-3.5 w-3.5"></i>
            {{ $isPositive ? '+' : '' }}{{ $trend }}%
        </span>
    </div>

    <div class="mt-5">
        <p class="text-sm font-medium text-slate-500">{{ $metric['label'] ?? 'Metric' }}</p>
        <p class="mt-1 text-3xl font-bold tracking-tight text-slate-950">{{ number_format((float) ($metric['value'] ?? 0)) }}</p>
    </div>

    <div class="mt-4 h-12" data-kpi-sparkline="{{ $metric['key'] ?? '' }}"></div>
</article>
