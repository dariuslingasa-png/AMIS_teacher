<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set New Password - AMIS Teacher Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/AMIS_Logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="teacher-login-body">
    <main class="teacher-login">
        <section class="teacher-login-panel" style="max-width: 480px; margin: 0 auto;">
            <div class="teacher-login-brand">
                <img src="{{ asset('images/AMIS_Logo.png') }}" alt="AMIS">
                <div>
                    <h1>Set New Password</h1>
                    <p>You are logging in with a temporary password. Please set a secure password.</p>
                </div>
            </div>

            @if($errors->any())
                <div class="teacher-error">
                    <i data-lucide="alert-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('teacher.login.change-password.store') }}" class="teacher-form">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="temp_password" value="{{ $temp_password }}">

                <div style="margin-bottom:18px;font-size:13px;font-weight:600;color:var(--t-text-secondary);">
                    Email: <span style="font-family:monospace;color:var(--t-text);font-weight:700;word-break:break-all;">{{ $email }}</span>
                </div>

                <label>
                    <span>New Password (min 8 characters)</span>
                    <input name="password" type="password" required autofocus>
                </label>
                <label>
                    <span>Confirm New Password</span>
                    <input name="password_confirmation" type="password" required>
                </label>
                <button type="submit" class="teacher-primary-btn" style="width:100%;">
                    <i data-lucide="shield-check"></i> Save Password & Continue
                </button>
            </form>
        </section>
    </main>
    <script>window.lucide?.createIcons();</script>
</body>
</html>