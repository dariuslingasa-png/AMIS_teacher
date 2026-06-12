@extends('teacher.layout', ['heading' => 'Announcements'])

@section('content')
<section class="teacher-split">
    <form method="POST" action="{{ route('teacher.announcements.store') }}" class="teacher-panel teacher-form">
        @csrf
        <div class="teacher-panel-header">
            <h2>Create Announcement</h2>
            <i data-lucide="megaphone" style="color:var(--t-text-muted);"></i>
        </div>
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
        <label><span>Title</span><input name="title" required placeholder="Class reminder..."></label>
        <label><span>Audience</span><input name="audience" value="Assigned students"></label>
        <label><span>Date</span><input name="date" type="date" value="{{ now()->toDateString() }}" required></label>
        <label><span>Message</span><textarea name="body" required placeholder="Write your announcement..."></textarea></label>
        <button type="submit" class="teacher-primary-btn"><i data-lucide="send"></i> Post Announcement</button>
    </form>

    <div class="teacher-panel">
        <div class="teacher-panel-header">
            <h2>Posted Updates</h2>
            <span>{{ $announcements->count() }} total</span>
        </div>
        <div class="teacher-announcement-list">
            @forelse($announcements as $announcement)
                <article>
                    <span>{{ $announcement['date'] }}</span>
                    <h3>{{ $announcement['title'] }}</h3>
                    <p>{{ $announcement['body'] }}</p>
                    <small>{{ $announcement['audience'] }}</small>
                </article>
            @empty
                <div class="dash-empty" style="padding: 48px 24px;">
                    <i data-lucide="megaphone-off" style="color: #10b981;"></i>
                    <p>No announcements posted yet</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
