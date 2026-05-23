import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const chartTheme = {
    green: '#059669',
    emerald: '#10b981',
    mint: '#a7f3d0',
    slate: '#64748b',
    blue: '#2563eb',
    amber: '#d97706',
    red: '#dc2626',
    grid: '#eef2f7',
};

const readDashboardData = () => {
    const node = document.getElementById('dashboard-chart-data');
    if (!node) return null;

    try {
        return JSON.parse(node.textContent);
    } catch {
        return null;
    }
};

const readJsonData = (id) => {
    const node = document.getElementById(id);
    if (!node) return null;

    try {
        return JSON.parse(node.textContent);
    } catch {
        return null;
    }
};

const baseChart = {
    chart: {
        fontFamily: 'Plus Jakarta Sans, sans-serif',
        toolbar: { show: false },
        animations: { enabled: false },
        foreColor: chartTheme.slate,
    },
    grid: {
        borderColor: chartTheme.grid,
        strokeDashArray: 4,
    },
    dataLabels: { enabled: false },
    legend: {
        fontWeight: 600,
        labels: { colors: chartTheme.slate },
    },
    tooltip: {
        theme: 'light',
    },
};

const mountChart = (ApexCharts, selector, options) => {
    const element = document.querySelector(selector);
    if (!element) return;
    new ApexCharts(element, options).render();
};

const money = (value) => `PHP ${Number(value || 0).toLocaleString()}`;

const renderDashboardCharts = async () => {
    const payload = readDashboardData();
    if (!payload) return;

    const { default: ApexCharts } = await import('apexcharts');
    const charts = payload.charts || {};
    const kpis = payload.kpis || [];

    mountChart(ApexCharts, '#enrollmentTrendChart', {
        ...baseChart,
        chart: { ...baseChart.chart, type: 'area', height: 320 },
        series: [{ name: 'Enrollments', data: charts.enrollmentTrend?.data || [] }],
        xaxis: { categories: charts.enrollmentTrend?.labels || [] },
        colors: [chartTheme.green],
        stroke: { width: 3, curve: 'smooth' },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: .34, opacityTo: .04 },
        },
    });

    mountChart(ApexCharts, '#gradeDistributionChart', {
        ...baseChart,
        chart: { ...baseChart.chart, type: 'bar', height: 320 },
        series: [{ name: 'Applications', data: charts.gradeDistribution?.data || [] }],
        xaxis: { categories: charts.gradeDistribution?.labels || [] },
        colors: [chartTheme.emerald],
        plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '58%' } },
    });

    mountChart(ApexCharts, '#statusDonutChart', {
        ...baseChart,
        chart: { ...baseChart.chart, type: 'donut', height: 320 },
        series: charts.statusDonut?.data || [],
        labels: charts.statusDonut?.labels || [],
        colors: [chartTheme.blue, chartTheme.green, chartTheme.red, chartTheme.amber],
        stroke: { width: 0 },
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        total: { show: true, label: 'Applications' },
                    },
                },
            },
        },
    });

    mountChart(ApexCharts, '#paymentTrendChart', {
        ...baseChart,
        chart: { ...baseChart.chart, type: 'line', height: 320 },
        series: [{ name: 'Payments', data: charts.paymentTrend?.data || [] }],
        xaxis: { categories: charts.paymentTrend?.labels || [] },
        yaxis: { labels: { formatter: money } },
        colors: [chartTheme.green],
        stroke: { width: 3, curve: 'smooth' },
        markers: { size: 4, colors: ['#fff'], strokeColors: chartTheme.green, strokeWidth: 2 },
        tooltip: { y: { formatter: money } },
    });

    kpis.forEach((metric) => {
        mountChart(ApexCharts, `[data-kpi-sparkline="${metric.key}"]`, {
            chart: {
                type: 'area',
                height: 48,
                sparkline: { enabled: true },
                animations: { enabled: false },
            },
            series: [{ data: metric.sparkline || [] }],
            colors: [chartTheme.green],
            stroke: { width: 2, curve: 'smooth' },
            fill: {
                type: 'gradient',
                gradient: { opacityFrom: .22, opacityTo: 0 },
            },
            tooltip: { enabled: false },
        });
    });
};

const renderApplicationDashboardCharts = async () => {
    const charts = readJsonData('application-dashboard-chart-data');
    if (!charts) return;

    const { default: ApexCharts } = await import('apexcharts');

    mountChart(ApexCharts, '#capacityRadialChart', {
        ...baseChart,
        chart: { ...baseChart.chart, type: 'radialBar', height: 320 },
        series: charts.capacity?.series || [0],
        labels: ['Capacity Used'],
        colors: [chartTheme.green],
        plotOptions: {
            radialBar: {
                hollow: { size: '64%' },
                track: { background: '#ecfdf5' },
                dataLabels: {
                    name: { fontSize: '13px', fontWeight: 700 },
                    value: { fontSize: '32px', fontWeight: 800, formatter: (value) => `${value}%` },
                    total: {
                        show: true,
                        label: `${charts.capacity?.enrolled || 0}/${charts.capacity?.capacity || 0} seats`,
                        formatter: () => `${charts.capacity?.series?.[0] || 0}%`,
                    },
                },
            },
        },
    });

    mountChart(ApexCharts, '#gradeCapacityChart', {
        ...baseChart,
        chart: { ...baseChart.chart, type: 'bar', height: 320, stacked: true },
        series: [
            { name: 'Enrolled', data: charts.gradeCapacity?.enrolled || [] },
            { name: 'Available', data: charts.gradeCapacity?.available || [] },
        ],
        xaxis: { categories: charts.gradeCapacity?.labels || [] },
        colors: [chartTheme.green, chartTheme.mint],
        plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '58%' } },
    });

    mountChart(ApexCharts, '#applicationFlowChart', {
        ...baseChart,
        chart: { ...baseChart.chart, type: 'area', height: 320 },
        series: [{ name: 'Applications', data: charts.applicationFlow?.data || [] }],
        xaxis: { categories: charts.applicationFlow?.labels || [] },
        colors: [chartTheme.green],
        stroke: { width: 3, curve: 'smooth' },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: .32, opacityTo: .04 },
        },
    });

    mountChart(ApexCharts, '#applicationTypeChart', {
        ...baseChart,
        chart: { ...baseChart.chart, type: 'donut', height: 320 },
        series: charts.typeBreakdown?.data || [],
        labels: charts.typeBreakdown?.labels || [],
        colors: [chartTheme.blue, chartTheme.green, chartTheme.amber, chartTheme.red],
        stroke: { width: 0 },
        plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Types' } } } } },
    });
};

document.addEventListener('DOMContentLoaded', () => {
    renderDashboardCharts();
    renderApplicationDashboardCharts();

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
