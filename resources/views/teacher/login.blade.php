<!DOCTYPE html>
@php
    $microsoftConfigured = filled(config('services.azure.client_id')) && filled(config('services.azure.client_secret'));
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - AMIS Teacher Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/AMIS_Logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;500;600;700;800;900&family=Noto+Naskh+Arabic:wght@600;700&display=swap" rel="stylesheet">
</head>
<body class="teacher-login-body">
    <main class="teacher-login">
        <section class="teacher-login-grid">
            <div class="teacher-login-identity">
                <div class="teacher-login-lockup">
                    <img src="{{ asset('images/AMIS_Logo.png') }}" alt="AMIS Logo">
                    <div class="teacher-login-wordmark">
                        <p class="teacher-login-arabic" lang="ar" dir="rtl">المدرسة الإسلامية المنورة</p>
                        <h1>AL MUNAWWARA ISLAMIC SCHOOL</h1>
                        <strong>Teacher Portal</strong>
                    </div>
                </div>
                <p>Manage subjects, meetings, grades, and class updates from one focused portal.</p>
            </div>

            <div class="teacher-login-panel">
                <div class="teacher-login-brand">
                    <div>
                        <h1>Sign in</h1>
                        <p>Use your AMIS teacher account to continue.</p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="teacher-error">
                        <i data-lucide="alert-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('teacher.login.store') }}" class="teacher-form">
                    @csrf
                    <label>
                        <span>Teacher Email</span>
                        <input name="teacher_id" value="{{ old('teacher_id', 'teacher@amis.edu.ph') }}" required autofocus>
                    </label>
                    <label>
                        <span>Password</span>
                        <input name="password" type="password" value="teacher123" required>
                    </label>
                    <button type="submit" class="teacher-primary-btn" style="width:100%;">
                        <i data-lucide="log-in"></i> Sign In
                    </button>
                </form>

                <div class="teacher-divider">or continue with</div>

                @if($microsoftConfigured)
                    <a href="{{ route('teacher.login.microsoft.redirect') }}" class="teacher-outline-btn" style="width:100%;display:flex;">
                        <svg style="width:16px;height:16px;" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0h11v11H0z" fill="#f25022"/><path d="M12 0h11v11H12z" fill="#7fba00"/>
                            <path d="M0 12h11v11H0z" fill="#00a4ef"/><path d="M12 12h11v11H12z" fill="#ffb900"/>
                        </svg>
                        Sign in with Microsoft
                    </a>
                @else
                    <button type="button" disabled class="teacher-outline-btn" style="width:100%;display:flex;opacity:0.5;cursor:not-allowed;">
                        <svg style="width:16px;height:16px;" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0h11v11H0z" fill="#888"/><path d="M12 0h11v11H12z" fill="#888"/>
                            <path d="M0 12h11v11H0z" fill="#888"/><path d="M12 12h11v11H12z" fill="#888"/>
                        </svg>
                        Microsoft sign-in not configured
                    </button>
                @endif
            </div>
        </section>
    </main>
    <script>window.lucide?.createIcons();</script>
</body>
</html>
