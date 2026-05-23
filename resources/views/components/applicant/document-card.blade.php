@props(['applicant', 'docKey', 'doc', 'status' => 'pending'])

@php
    $url = $doc['url'] ?? null;
    $label = $doc['label'] ?? 'Document';
    $assetUrl = $url ? asset('storage/'.$url) : null;
    $isPdf = $url && strtolower(pathinfo($url, PATHINFO_EXTENSION)) === 'pdf';
    $statusColor = ['approved' => 'green', 'rejected' => 'red', 'pending' => 'yellow'][$status] ?? 'gray';
@endphp

<article class="upload-card {{ $url ? '' : 'upload-card-missing' }}">
    <button type="button"
            class="upload-preview"
            @if ($assetUrl) @click="openPreview('{{ $assetUrl }}', '{{ $label }}', {{ $isPdf ? 'true' : 'false' }})" @endif
            @disabled(!$assetUrl)>
        @if ($assetUrl && !$isPdf)
            <img src="{{ $assetUrl }}" alt="{{ $label }}">
        @elseif ($assetUrl && $isPdf)
            <span class="upload-pdf">
                <i data-lucide="file-text" class="h-9 w-9"></i>
                PDF Document
            </span>
        @else
            <span class="upload-empty">
                <i data-lucide="upload-cloud" class="h-8 w-8"></i>
                Not uploaded
            </span>
        @endif
    </button>

    <div class="upload-body">
        <div class="flex items-center justify-between gap-3">
            <h3>{{ $label }}</h3>
            <x-badge :color="$statusColor">{{ ucfirst($status) }}</x-badge>
        </div>

        @if ($assetUrl)
            <div class="mt-3 flex gap-2">
                <a href="{{ $assetUrl }}" target="_blank" class="doc-action doc-action-view">Open</a>
                <form method="POST" action="{{ route('admin.applicants.document', $applicant) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="doc_key" value="{{ $docKey }}">
                    <input type="hidden" name="status" value="approved">
                    <button class="doc-action doc-action-approve">Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.applicants.document', $applicant) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="doc_key" value="{{ $docKey }}">
                    <input type="hidden" name="status" value="rejected">
                    <button class="doc-action doc-action-reject">Reject</button>
                </form>
            </div>
        @endif
    </div>
</article>
