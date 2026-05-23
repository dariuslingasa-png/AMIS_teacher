<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800']) }}>
    <table class="amis-table">
        {{ $slot }}
    </table>
</div>
