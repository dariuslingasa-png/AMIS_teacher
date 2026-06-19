@extends('teacher.layout', ['heading' => 'Student Roster'])

@section('content')
<div class="teacher-panel" style="display:flex; flex-direction:column; gap:32px;">
    
    @if($advisorySections->isNotEmpty())
        @foreach($advisorySections as $group)
            <div>
                <div class="teacher-panel-header" style="margin-bottom:16px;">
                    <h2 style="font-size:16px; font-weight:800; color:var(--t-primary, #10b981); display:flex; align-items:center; gap:8px;">
                        <i data-lucide="shield-check" style="width:18px;height:18px;"></i>
                        {{ $group['title'] }}
                    </h2>
                    <span>{{ $group['students']->count() }} students</span>
                </div>
                
                <div class="teacher-roster-grid">
                    @foreach($group['students'] as $student)
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
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

    @if($subjectSections->isNotEmpty())
        @foreach($subjectSections as $group)
            <div>
                <div class="teacher-panel-header" style="margin-bottom:16px;">
                    <h2 style="font-size:16px; font-weight:800; color:var(--t-sky, #3b82f6); display:flex; align-items:center; gap:8px;">
                        <i data-lucide="book-open" style="width:18px;height:18px;"></i>
                        {{ $group['title'] }}
                    </h2>
                    <span>{{ $group['students']->count() }} students</span>
                </div>
                
                <div class="teacher-roster-grid">
                    @foreach($group['students'] as $student)
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
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

    @if($advisorySections->isEmpty() && $subjectSections->isEmpty())
        <div class="dash-empty" style="padding: 48px 24px; text-align: center;">
            <i data-lucide="users-round" style="color: #10b981;"></i>
            <p>No students in your roster</p>
        </div>
    @endif

</div>
@endsection