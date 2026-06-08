@extends('teacher.layout', ['heading' => 'Student Roster'])

@section('content')
<div class="teacher-panel">
    <div class="teacher-panel-header">
        <h2>Roster</h2>
        <span>{{ $students->count() }} students</span>
    </div>
    <div class="teacher-roster-grid">
        @forelse($students as $student)
            <article>
                <div class="teacher-avatar" style="width:44px;height:44px;font-size:16px;">
                    {{ collect(explode(' ', $student['name']))->map(fn($p) => $p[0] ?? '')->implode('') }}
                </div>
                <div>
                    <h3>{{ $student['name'] }}</h3>
                    <p>{{ $student['student_no'] }}</p>
                    <small>{{ $student['grade'] }} · {{ $student['section'] }}</small>
                </div>
            </article>
        @empty
            <div style="grid-column:1/-1;padding:48px;text-align:center;color:var(--t-text-muted);">
                <i data-lucide="users-round" style="width:40px;height:40px;margin-bottom:12px;"></i>
                <p style="margin:0;font-size:15px;font-weight:600;">No students in your roster</p>
            </div>
        @endforelse
    </div>
</div>
@endsection