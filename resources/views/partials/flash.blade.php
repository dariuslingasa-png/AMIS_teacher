@if (session('success'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
@endif

@if (session('status'))
    <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ $errors->first() }}</div>
@endif
