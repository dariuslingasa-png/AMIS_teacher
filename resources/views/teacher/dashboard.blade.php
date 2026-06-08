@extends('teacher.layout', ['heading' => 'Dashboard'])

@section('content')
@php
    $teacherName = session('teacher_name', 'Teacher');
    $cleanName = preg_replace('/^(teacher|ust\.|ustadz\.?|ustadh\.?|sir\.?|ma\'am\.?|maam\.?|ms\.?|mrs\.?|mr\.?)\s+/i', '', trim($teacherName));
    $firstName = explode(' ', $cleanName)[0];
    $cards = [
        ['My Subjects', $subjects->count(), 'book-open-check', 'green'],
        ['Upcoming Meetings', $meetings->whereNotIn('status', ['Completed'])->count(), 'video', 'blue'],
        ['Recent Announcements', $announcements->count(), 'megaphone', 'violet'],
        ['Student Count', $students->count(), 'users-round', 'violet'],
        ['Pending Activities', $assessments->count(), 'clipboard-list', 'amber'],
    ];
@endphp

{{-- Welcome Hero --}}
<section class="dash-welcome">
    <div class="dash-welcome-body">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider bg-slate-900 text-white rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                {{ session('teacher_dept') }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider bg-emerald-600 text-white rounded-full">
                Assalamualaikum, {{ $firstName }}
            </span>
        </div>
        <h2 class="dash-welcome-title">Your Teaching Dashboard</h2>
        <p class="dash-welcome-sub">Manage subjects, schedule meetings, track grades, and keep your students informed — all in one place.</p>
    </div>
    <div class="dash-welcome-actions">
        <a href="{{ route('teacher.meetings') }}" class="teacher-primary-btn"><i data-lucide="video"></i> Create Meeting</a>
        <a href="{{ route('teacher.grades') }}" class="teacher-light-btn"><i data-lucide="edit-3"></i> Gradebook</a>
    </div>
    <img src="{{ asset('images/school_elements_bg.png') }}" class="dash-welcome-pattern" alt="School elements pattern">
</section>

{{-- Quick Actions --}}
<section class="dash-actions">
    <a href="{{ route('teacher.subjects') }}" class="dash-action dash-action-subjects">
        <span class="dash-action-icon"><i data-lucide="book-open-check"></i></span>
        <span class="dash-action-text"><strong>Manage Subjects</strong><span>Add or view subjects</span></span>
    </a>
    <a href="{{ route('teacher.meetings') }}" class="dash-action dash-action-meetings">
        <span class="dash-action-icon"><i data-lucide="video"></i></span>
        <span class="dash-action-text"><strong>Schedule Meeting</strong><span>Create class meetings</span></span>
    </a>
    <a href="{{ route('teacher.announcements') }}" class="dash-action dash-action-announcements">
        <span class="dash-action-icon"><i data-lucide="megaphone"></i></span>
        <span class="dash-action-text"><strong>Post Update</strong><span>Notify your students</span></span>
    </a>
</section>

{{-- Stats Grid --}}
<section class="dash-stats">
    @foreach($cards as [$label, $value, $icon, $tone])
        <article class="dash-stat dash-stat-{{ $tone }}">
            <span class="dash-stat-icon"><i data-lucide="{{ $icon }}"></i></span>
            <div>
                <p class="dash-stat-label">{{ $label }}</p>
                <strong class="dash-stat-value">{{ $value }}</strong>
            </div>
        </article>
    @endforeach
</section>

{{-- Main Content Grid --}}
<div class="dash-grid">
    <div class="dash-main-col">
        {{-- Subjects Panel --}}
        <div class="dash-panel">
            <div class="dash-panel-header">
                <h2>Subject Load</h2>
                <a href="{{ route('teacher.subjects') }}" class="dash-panel-link">{{ $subjects->count() }} subjects →</a>
            </div>
            <div class="dash-subject-list">
                @forelse($subjects->take(5) as $subject)
                    <a href="{{ route('teacher.subjects') }}" class="dash-subject">
                        <span class="dash-subject-code">{{ $subject['code'] ?: substr($subject['name'] ?? 'SUB', 0, 3) }}</span>
                        <span class="dash-subject-info">
                            <strong>{{ $subject['name'] }}</strong>
                            <span>{{ $subject['grade'] }} · {{ $subject['section'] }} · {{ $subject['schedule'] ?? 'Unscheduled' }}</span>
                        </span>
                    </a>
                @empty
                    <div class="dash-empty">
                        <i data-lucide="book-open"></i>
                        <p>No subjects assigned yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Meetings Timeline --}}
        <div class="dash-panel">
            <div class="dash-panel-header">
                <h2>Upcoming Meetings</h2>
                <a href="{{ route('teacher.meetings') }}" class="dash-panel-link">View all →</a>
            </div>
            <div class="dash-timeline">
                @forelse($meetings->take(5) as $meeting)
                    @php $subject = $subjects->firstWhere('id', $meeting['subject_id']); @endphp
                    <div class="dash-timeline-item">
                        <span class="dash-timeline-dot {{ strtolower($meeting['status'] ?? 'scheduled') }}"></span>
                        <div class="dash-timeline-content">
                            <strong>{{ $meeting['title'] }}</strong>
                            <p>{{ $subject['name'] ?? 'No subject' }} · {{ $meeting['status'] ?? 'Scheduled' }}</p>
                        </div>
                        <span class="dash-timeline-time">{{ \Illuminate\Support\Str::of($meeting['date'])->substr(5) }} · {{ $meeting['time'] }}</span>
                    </div>
                @empty
                    <div class="dash-empty">
                        <i data-lucide="calendar-off"></i>
                        <p>No meetings scheduled</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right Sidebar --}}
    <div class="dash-sidebar-col">
        {{-- Profile Widget --}}
        <div class="dash-profile-widget">
            <div class="dash-profile-avatar">{{ strtoupper(substr(session('teacher_name', $teacherName), 0, 2)) }}</div>
            <h3>{{ session('teacher_name', $teacherName) }}</h3>
            <p>{{ session('teacher_dept') }}</p>
            <div class="dash-profile-stats">
                <div class="dash-profile-stat"><strong>{{ $subjects->count() }}</strong><span>Subjects</span></div>
                <div class="dash-profile-stat"><strong>{{ $students->count() }}</strong><span>Students</span></div>
                <div class="dash-profile-stat"><strong>{{ $meetings->count() }}</strong><span>Meetings</span></div>
            </div>
        </div>

        {{-- Recent Activity Feed --}}
        <div class="dash-panel">
            <div class="dash-panel-header">
                <h2>Recent Activity</h2>
                <a href="{{ route('teacher.announcements') }}" class="dash-panel-link">All →</a>
            </div>
            <div class="dash-feed">
                @forelse((collect($announcements ?? [])->sortByDesc('date')->take(5)) as $item)
                    <div class="dash-feed-item">
                        <strong>{{ $item['title'] }}</strong>
                        <p>{{ Str::limit($item['body'], 80) }}</p>
                        <div class="dash-feed-meta"><span>{{ $item['date'] }}</span><span>{{ $item['audience'] }}</span></div>
                    </div>
                @empty
                    <div class="dash-empty"><i data-lucide="megaphone"></i><p>No announcements yet</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
