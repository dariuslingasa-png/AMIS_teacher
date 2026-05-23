@props(['title', 'icon' => 'folder', 'fields' => []])

<section class="detail-section">
    <h3>
        <span class="detail-section-icon">
            <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
        </span>
        {{ $title }}
    </h3>
    <dl class="detail-grid">
        @foreach ($fields as [$label, $value])
            <x-applicant.field :label="$label" :value="$value" />
        @endforeach
    </dl>
</section>
