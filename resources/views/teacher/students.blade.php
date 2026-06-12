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
                <div class="teacher-avatar" style="width:44px;height:44px;font-size:16px;overflow:hidden;position:relative;display:flex;align-items:center;justify-content:center;">
                    @php
                        $initials = collect(explode(' ', $student['name']))->map(fn($p) => $p[0] ?? '')->implode('');
                    @endphp
                    <span class="avatar-initials" style="font-weight:750;">{{ $initials }}</span>
                    @if(!empty($student['photo_url']))
                        <img src="{{ $student['photo_url'] }}" alt="{{ $student['name'] }}"
                             class="absolute inset-0 h-full w-full object-cover block"
                             style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;"
                             onerror="this.style.display='none';">
                    @endif
                </div>
                <div>
                    <h3>{{ $student['name'] }}</h3>
                    <p>{{ $student['student_no'] }}</p>
                    <small>{{ $student['grade'] }} · {{ $student['section'] }}</small>
                </div>
            </article>
        @empty
            <div class="dash-empty" style="grid-column:1/-1; padding: 48px 24px;">
                <i data-lucide="users-round" style="color: #10b981;"></i>
                <p>No students in your roster</p>
            </div>
        @endforelse
    </div>
</div>
@endsection