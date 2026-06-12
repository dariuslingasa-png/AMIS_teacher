@extends('teacher.layout', ['heading' => $heading ?? 'Feature'])

@section('content')
<div class="teacher-panel" style="min-height: 480px; display: flex; align-items: center; justify-content: center; background: white; border: 1px solid #e9ebee; border-radius: var(--r-lg);">
    <div style="padding: 48px 24px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; max-width: 480px;">
        <div style="background-color: rgba(16, 185, 129, 0.1); border-radius: 50%; padding: 20px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px; box-shadow: 0 0 16px rgba(16, 185, 129, 0.08); animation: pulse-ring 2.5s infinite;">
            <i data-lucide="{{ $icon ?? 'rocket' }}" style="width: 44px; height: 44px; color: #10b981; stroke-width: 1.5;"></i>
        </div>
        <h3 style="font-size: 1.5rem; font-weight: 800; color: #0d1117; margin: 0; letter-spacing: -0.5px;">{{ $heading }} Module</h3>
        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 99px; color: #059669; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
            <span style="width: 5px; height: 5px; border-radius: 50%; background-color: #10b981;"></span>
            Coming Soon
        </span>
        <p style="font-size: 0.95rem; color: #57606a; margin: 8px 0 0; line-height: 1.6;">
            We are putting the final touches on the <strong>{{ $heading }}</strong> module. This feature will be released in the next platform update.
        </p>
        <div style="margin-top: 16px;">
            <a href="{{ route('teacher.dashboard') }}" class="teacher-primary-btn" style="min-height: 40px; border-radius: 10px; font-size: 13.5px; font-weight: 600;">
                <i data-lucide="layout-dashboard"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<style>
@keyframes pulse-ring {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.15); }
    50% { transform: scale(1.03); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
}
</style>
@endsection
