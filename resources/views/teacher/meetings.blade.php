@extends('teacher.layout', ['heading' => 'Class Meetings'])

@section('content')
<section class="teacher-table-panel teacher-meetings-panel">
    <div class="teacher-panel-header">
        <div>
            <h2>Meeting Board</h2>
            <span>{{ $meetings->count() }} records</span>
        </div>
        @if($subjects->isNotEmpty())
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <form method="POST" action="{{ route('teacher.meetings.store') }}" style="margin:0;">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $subjects->first()['id'] }}">
                    <input type="hidden" name="title" value="Instant Meeting">
                    <input type="hidden" name="description" value="Live class meeting started from the Faculty Portal.">
                    <input type="hidden" name="date" value="{{ now()->toDateString() }}">
                    <input type="hidden" name="time" value="{{ now()->format('H:i') }}">
                    <input type="hidden" name="duration" value="60">
                    <input type="hidden" name="status" value="Live">
                    <button class="teacher-primary-btn"><i data-lucide="play"></i> Start Now</button>
                </form>
                <button type="button" class="teacher-primary-btn" data-teacher-modal-open aria-controls="meetingCreateModal">
                    <i data-lucide="plus"></i> Schedule
                </button>
            </div>
        @endif
    </div>


    <div class="teacher-table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Meeting</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Link</th>
                </tr>
            </thead>
            <tbody>
                @forelse($meetings as $meeting)
                    @php $subject = $subjects->firstWhere('id', $meeting['subject_id']); @endphp
                    <tr>
                        <td><span class="teacher-status-pill">{{ $meeting['status'] }}</span></td>
                        <td><strong>{{ $meeting['title'] }}</strong></td>
                        <td>
                            {{ $subject['name'] ?? 'No subject' }}
                            <span>{{ $subject['section'] ?? 'Unassigned' }}</span>
                        </td>
                        <td>{{ $meeting['date'] }}</td>
                        <td>{{ $meeting['time'] }}</td>
                        <td class="teacher-table-muted">{{ $meeting['agenda'] ?: 'No description added' }}</td>
                        <td>
                            @if($meeting['link'])
                                <a href="{{ $meeting['link'] }}" target="_blank" class="teacher-outline-btn">
                                    <i data-lucide="external-link"></i> Open
                                </a>
                            @else
                                <span>No link</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            @if($subjects->isEmpty())
                                <div style="padding: 48px 24px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;">
                                    <div style="background-color: rgba(16, 185, 129, 0.1); border-radius: 50%; padding: 16px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i data-lucide="video" style="width: 36px; height: 36px; color: #10b981; stroke-width: 1.5;"></i>
                                    </div>
                                    <h3 style="font-size: 1.15rem; font-weight: 600; color: #e2e8f0; margin: 0;">Preparing Academic Load</h3>
                                    <p style="font-size: 0.875rem; color: #94a3b8; max-width: 400px; margin: 0; line-height: 1.5;">Please wait for the administrator in the Admin Portal to assign subjects to your account first.</p>
                                </div>
                            @else
                                <div class="dash-empty">
                                    <i data-lucide="video-off"></i>
                                    <p>No meetings created yet</p>
                                    <button type="button" class="teacher-primary-btn" data-teacher-modal-open aria-controls="meetingCreateModal">
                                        <i data-lucide="plus"></i> Add Meeting
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@if($subjects->isNotEmpty())
<div id="meetingCreateModal" class="teacher-modal-backdrop" data-teacher-modal hidden>
    <div class="teacher-modal-card" role="dialog" aria-modal="true" aria-labelledby="meetingCreateTitle">
        <div class="teacher-panel-header">
            <h2 id="meetingCreateTitle">Create Meeting</h2>
            <button type="button" class="teacher-icon-btn" data-teacher-modal-close aria-label="Close">
                <i data-lucide="x"></i>
            </button>
        </div>

        @if($errors->any())
            <div class="teacher-error">
                <i data-lucide="alert-circle"></i>
                <span>Please check the meeting details and try again.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.meetings.store') }}" class="teacher-form">
            @csrf
            <label>
                <span>Subject</span>
                <select name="subject_id" required>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject['id'] }}" @selected(old('subject_id') === $subject['id'])>
                            {{ $subject['name'] }} · {{ $subject['section'] }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label><span>Meeting Title</span><input name="title" value="{{ old('title') }}" required placeholder="Weekly review"></label>
            <div class="teacher-form-grid">
                <label><span>Date</span><input name="date" type="date" value="{{ old('date', now()->toDateString()) }}" required></label>
                <label><span>Time</span><input name="time" type="time" value="{{ old('time', '08:00') }}" required></label>
            </div>
            <label><span>Duration</span><input name="duration" type="number" min="5" max="480" value="{{ old('duration', 60) }}" required></label>
            <label><span>Meeting Link</span><input name="link" type="url" value="{{ old('link') }}" placeholder="https://teams.microsoft.com/..."></label>
            <label><span>Description</span><textarea name="description" placeholder="Discussion points">{{ old('description', old('agenda')) }}</textarea></label>
            <label>
                <span>Status</span>
                <select name="status">
                    @foreach(['Scheduled','Live','Draft','Completed'] as $status)
                        <option @selected(old('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </label>
            <div class="teacher-modal-actions">
                <button type="button" class="teacher-outline-btn" data-teacher-modal-close>Cancel</button>
                <button type="submit" class="teacher-primary-btn"><i data-lucide="save"></i> Save Meeting</button>
            </div>
        </form>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('meetingCreateModal');
        if (!modal) return;

        const openModal = () => {
            modal.hidden = false;
            document.body.classList.add('teacher-modal-open');
            window.lucide?.createIcons();
            modal.querySelector('select[name="subject_id"]')?.focus();
        };

        const closeModal = () => {
            modal.hidden = true;
            document.body.classList.remove('teacher-modal-open');
        };

        document.querySelectorAll('[data-teacher-modal-open]').forEach((button) => {
            button.addEventListener('click', openModal);
        });

        modal.querySelectorAll('[data-teacher-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });

        if (@json($errors->any())) {
            openModal();
        }
    })();
</script>
@endif
@endsection
