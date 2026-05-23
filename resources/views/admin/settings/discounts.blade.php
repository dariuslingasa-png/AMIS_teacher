<x-admin-layout title="Discount Settings">
    <x-card title="Sibling Discount Settings" subtitle="Configure automatic discount percentages">
        <form method="POST" action="{{ route('admin.settings.discounts.update') }}" class="grid gap-4 md:grid-cols-2">
            @csrf
            @method('PATCH')
            @foreach ([
                'second_child_percentage' => 'Second child percentage',
                'third_child_percentage' => 'Third child percentage',
                'fourth_child_percentage' => 'Fourth child percentage',
            ] as $field => $label)
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-900">{{ $label }}</label>
                    <input name="{{ $field }}" type="number" min="0" max="100" value="{{ old($field, $setting->{$field} ?? 0) }}" class="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm">
                </div>
            @endforeach
            <label class="flex items-center gap-2 text-sm font-medium text-gray-900">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $setting->is_active ?? false)) class="rounded border-gray-300 text-primary-600">
                Active
            </label>
            <div class="md:col-span-2">
                <button class="rounded-lg bg-primary-700 px-4 py-2 text-sm font-medium text-white">Save settings</button>
            </div>
        </form>
    </x-card>
</x-admin-layout>
