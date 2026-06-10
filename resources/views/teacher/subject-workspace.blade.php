@extends('teacher.layout', ['heading' => $subject['name']])

@section('content')
@php
    $tab = request('tab', 'overview');
    $tabs = ['overview', 'announcements', 'materials', 'assignments', 'meetings', 'students', 'grades', 'attendance'];
@endphp

<section class="dash-welcome">
    <div class="dash-welcome-body">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="teacher-subject-code-pill">{{ $subject['code'] ?: strtoupper(substr($subject['name'], 0, 3)) }}</span>
            <span class="teacher-subject-list-chip">{{ $subject['mode'] }}</span>
        </div>
        <h2 class="dash-welcome-title">{{ $subject['name'] }}</h2>
        <p class="dash-welcome-sub">{{ $subject['grade'] ?: 'Catalog' }} · {{ $subject['section'] }} · {{ $subject['schedule'] ?: 'Unscheduled' }}</p>
    </div>
    <div class="dash-welcome-actions">
        <a href="{{ route('teacher.subjects') }}" class="teacher-light-btn"><i data-lucide="arrow-left"></i> Classroom Workspace</a>
        <a href="{{ route('teacher.grades', ['subject' => $subject['id']]) }}" class="teacher-primary-btn"><i data-lucide="clipboard-list"></i> Gradebook</a>
    </div>
</section>

<nav class="dash-actions">
    @foreach($tabs as $item)
        <a href="{{ route('teacher.subjects.workspace', ['subject' => $subject['id'], 'tab' => $item]) }}" class="dash-action {{ $tab === $item ? 'dash-action-subjects' : '' }}">
            <span class="dash-action-icon"><i data-lucide="{{ [
                'overview' => 'layout-dashboard',
                'announcements' => 'megaphone',
                'materials' => 'folder-open',
                'assignments' => 'file-check-2',
                'meetings' => 'video',
                'students' => 'users',
                'grades' => 'clipboard-list',
                'attendance' => 'calendar-check',
            ][$item] }}"></i></span>
            <span class="dash-action-text"><strong>{{ Str::headline($item) }}</strong></span>
        </a>
    @endforeach
</nav>

@if($tab === 'overview')
    <section class="dash-stats">
        <article class="dash-stat dash-stat-green"><span class="dash-stat-icon"><i data-lucide="folder-open"></i></span><div><p class="dash-stat-label">Materials</p><strong class="dash-stat-value">{{ $subjectMaterials->count() }}</strong></div></article>
        <article class="dash-stat dash-stat-blue"><span class="dash-stat-icon"><i data-lucide="video"></i></span><div><p class="dash-stat-label">Meetings</p><strong class="dash-stat-value">{{ $subjectMeetings->count() }}</strong></div></article>
        <article class="dash-stat dash-stat-violet"><span class="dash-stat-icon"><i data-lucide="users"></i></span><div><p class="dash-stat-label">Students</p><strong class="dash-stat-value">{{ $subjectStudents->count() }}</strong></div></article>
        <article class="dash-stat dash-stat-amber"><span class="dash-stat-icon"><i data-lucide="megaphone"></i></span><div><p class="dash-stat-label">Announcements</p><strong class="dash-stat-value">{{ $subjectAnnouncements->count() }}</strong></div></article>
    </section>
@endif

@if($tab === 'materials')
    <section class="teacher-split">
        <form method="POST" action="{{ route('teacher.materials.store') }}" enctype="multipart/form-data" class="teacher-panel teacher-form">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $subject['id'] }}">
            <div class="teacher-panel-header"><h2>Publish Material</h2><i data-lucide="folder-up" style="color:var(--t-text-muted);"></i></div>
            <label><span>Title</span><input name="title" required value="{{ old('title') }}"></label>
            <label><span>Description</span><textarea name="description">{{ old('description') }}</textarea></label>
            <label><span>File</span><input name="file" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,image/*,video/*"></label>
            <label><span>Google Drive Link</span><input name="external_url" type="url" value="{{ old('external_url') }}" placeholder="https://drive.google.com/..."></label>
            <button type="submit" class="teacher-primary-btn"><i data-lucide="upload-cloud"></i> Publish</button>
        </form>
        <div class="teacher-panel">
            <div class="teacher-panel-header"><h2>Learning Materials</h2><span>{{ $subjectMaterials->count() }} total</span></div>
            <div class="teacher-announcement-list">
                @forelse($subjectMaterials as $material)
                    <article>
                        <span>{{ $material['created_at'] }}</span>
                        <h3>{{ $material['title'] }}</h3>
                        <p>{{ $material['description'] ?: Str::headline($material['type']) }}</p>
                        <a href="{{ $material['url'] }}" target="_blank" class="teacher-outline-btn"><i data-lucide="external-link"></i> Open</a>
                    </article>
                @empty
                    <div class="dash-empty"><i data-lucide="folder-open"></i><p>No materials published yet</p></div>
                @endforelse
            </div>
        </div>
    </section>
@endif

@if($tab === 'meetings')
    <section class="teacher-split">
        <div class="teacher-panel">
            <div class="teacher-panel-header"><h2>Start Now</h2><i data-lucide="radio" style="color:var(--t-text-muted);"></i></div>
            <form method="POST" action="{{ route('teacher.meetings.store') }}" class="teacher-form">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject['id'] }}">
                <input type="hidden" name="date" value="{{ now()->toDateString() }}">
                <input type="hidden" name="time" value="{{ now()->format('H:i') }}">
                <input type="hidden" name="duration" value="60">
                <input type="hidden" name="status" value="Live">
                <label><span>Title</span><input name="title" required value="Live {{ $subject['name'] }}"></label>
                <label><span>Microsoft Teams Link</span><input name="link" type="url" placeholder="https://teams.microsoft.com/..."></label>
                <button type="submit" class="teacher-primary-btn"><i data-lucide="video"></i> Start Meeting</button>
            </form>
        </div>
        <div class="teacher-panel">
            <div class="teacher-panel-header"><h2>Meetings</h2><span>{{ $subjectMeetings->count() }} total</span></div>
            <div class="dash-timeline">
                @forelse($subjectMeetings as $meeting)
                    <div class="dash-timeline-item">
                        <span class="dash-timeline-dot {{ strtolower($meeting['status']) }}"></span>
                        <div class="dash-timeline-content"><strong>{{ $meeting['title'] }}</strong><p>{{ $meeting['agenda'] ?: $meeting['status'] }}</p></div>
                        <span class="dash-timeline-time">{{ $meeting['date'] }} · {{ $meeting['time'] }}</span>
                    </div>
                @empty
                    <div class="dash-empty"><i data-lucide="calendar-off"></i><p>No meetings yet</p></div>
                @endforelse
            </div>
        </div>
    </section>
@endif

@if($tab === 'announcements')
    <section class="teacher-split">
        <form method="POST" action="{{ route('teacher.announcements.store') }}" class="teacher-panel teacher-form">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $subject['id'] }}">
            <div class="teacher-panel-header"><h2>Post Announcement</h2><i data-lucide="megaphone" style="color:var(--t-text-muted);"></i></div>
            <label><span>Title</span><input name="title" required></label>
            <label><span>Audience</span><input name="audience" value="{{ $subject['name'] }} students" required></label>
            <label><span>Date</span><input name="date" type="date" value="{{ now()->toDateString() }}" required></label>
            <label><span>Message</span><textarea name="body" required></textarea></label>
            <button type="submit" class="teacher-primary-btn"><i data-lucide="send"></i> Post</button>
        </form>
        <div class="teacher-panel">
            <div class="teacher-panel-header"><h2>Announcements</h2><span>{{ $subjectAnnouncements->count() }} total</span></div>
            <div class="teacher-announcement-list">
                @forelse($subjectAnnouncements as $announcement)
                    <article><span>{{ $announcement['date'] }}</span><h3>{{ $announcement['title'] }}</h3><p>{{ $announcement['body'] }}</p></article>
                @empty
                    <div class="dash-empty"><i data-lucide="megaphone-off"></i><p>No announcements yet</p></div>
                @endforelse
            </div>
        </div>
    </section>
@endif

@if(in_array($tab, ['assignments', 'students', 'grades', 'attendance'], true))
    <section class="teacher-table-panel">
        <div class="teacher-panel-header">
            <div><h2>{{ Str::headline($tab) }}</h2><span>{{ $subject['name'] }}</span></div>
        </div>
        <div class="dash-empty">
            <i data-lucide="{{ $tab === 'students' ? 'users' : ($tab === 'grades' ? 'clipboard-list' : 'file-check-2') }}"></i>
            <p>{{ $tab === 'students' ? $subjectStudents->count().' enrolled students' : 'No records yet' }}</p>
        </div>
    </section>
@endif
@endsection
