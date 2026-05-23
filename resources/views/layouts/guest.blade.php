<x-app-layout :title="$title ?? 'AMIS Admin'">
    <main class="min-h-screen bg-gray-50 dark:bg-gray-900">
        {{ $slot }}
    </main>
</x-app-layout>
