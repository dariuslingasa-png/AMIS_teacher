<x-admin-layout title="Create MS Team">
    <x-card title="Create MS Team">
        <form method="POST" action="{{ route('admin.ms-teams.store') }}" class="grid gap-4 md:grid-cols-2">
            @csrf
            <input name="grade_level" placeholder="Grade level" class="rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm">
            <input name="learning_mode" placeholder="Learning mode" class="rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm">
            <input name="name" placeholder="Section name" class="rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm">
            <input name="school_year" placeholder="School year" class="rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm">
            <div class="md:col-span-2"><button class="rounded-lg bg-primary-700 px-4 py-2 text-sm font-medium text-white">Create</button></div>
        </form>
    </x-card>
</x-admin-layout>
