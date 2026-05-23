<x-app-layout :title="$title ?? 'Dashboard'">
    @include('partials.topbar')

    <div class="admin-shell">
        @include('partials.sidebar')

        <div class="admin-content bg-gray-50 dark:bg-gray-900">
            <main class="p-4 md:p-6">
                <div class="mx-auto max-w-screen-2xl">
                    @include('partials.flash')
                    @include('partials.breadcrumbs')
                    {{ $slot }}
                </div>
            </main>

            @include('partials.footer')
        </div>
    </div>
</x-app-layout>
