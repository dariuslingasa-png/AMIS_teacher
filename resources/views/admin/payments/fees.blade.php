<x-admin-layout title="Fee & Discount">
    @include('admin.payments.partials.fees-styles')
    <div class="space-y-6 print:space-y-0">
        @include('admin.payments.partials.fees-toolbar')
        @include('admin.payments.partials.fees-sheet')
    </div>
</x-admin-layout>
