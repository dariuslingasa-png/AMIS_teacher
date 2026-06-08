@extends('teacher.layout', ['heading' => 'My Subjects'])

@section('content')
<section class="teacher-table-panel teacher-subjects-panel">
    <div class="teacher-panel-header">
        <div>
            <h2>Assigned Subjects</h2>
            <span>{{ $subjects->count() }} total</span>
        </div>
    </div>

    <div class="teacher-table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Subject</th>
                    <th>Grade / Section</th>
                    <th>Schedule</th>
                    <th>Mode</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                    <tr>
                        <td>
                            <span class="teacher-subject-code-pill">
                                {{ $subject['code'] ?: strtoupper(substr($subject['name'] ?? 'SUB', 0, 3)) }}
                            </span>
                        </td>
                        <td><strong>{{ $subject['name'] }}</strong></td>
                        <td>
                            {{ $subject['grade'] ?: 'Catalog' }}
                            <span>{{ $subject['section'] }}</span>
                        </td>
                        <td>{{ $subject['schedule'] ?: 'Unscheduled' }}</td>
                        <td><span class="teacher-subject-list-chip">{{ $subject['mode'] }}</span></td>
                        <td>
                            <a href="{{ route('teacher.subjects.workspace', $subject['id']) }}" class="teacher-outline-btn">
                                <i data-lucide="layout-dashboard"></i> Open
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="dash-empty">
                                <i data-lucide="book-open"></i>
                                <p>No subjects assigned yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
