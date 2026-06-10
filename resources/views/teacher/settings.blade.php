@extends('teacher.layout', ['heading' => 'Settings'])

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">
    @if($errors->has('microsoft'))
        <div class="teacher-error" style="margin-bottom: 24px;">
            <i data-lucide="alert-circle"></i>
            {{ $errors->first('microsoft') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 28px; align-items: start;">
        
        {{-- Left: Account & Security --}}
        <div style="display: flex; flex-direction: column; gap: 28px;">
            
            {{-- Account Information --}}
            <div style="background: var(--s-surface, #ffffff); border: 1px solid var(--s-border, #e2e8f0); border-radius: var(--r, 12px); padding: 24px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; border-bottom: 1px solid var(--s-border, #e2e8f0); padding-bottom: 16px;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: #e0f2fe; color: #0284c7;">
                        <i data-lucide="user" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: var(--t-primary, #0f172a);">Account Profile</h3>
                        <p style="margin: 2px 0 0; font-size: 12px; color: var(--t-tertiary, #64748b);">Your personal details on the AMIS system.</p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px;">
                    <div>
                        <span style="display: block; font-size: 11px; font-weight: 650; color: var(--t-tertiary, #64748b); text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 2px;">Full Name</span>
                        <strong style="color: var(--t-primary, #0f172a); font-size: 15px;">{{ $user->name }}</strong>
                    </div>
                    <div>
                        <span style="display: block; font-size: 11px; font-weight: 650; color: var(--t-tertiary, #64748b); text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 2px;">Email Address</span>
                        <strong style="color: var(--t-primary, #0f172a); font-size: 15px;">{{ $user->email }}</strong>
                    </div>
                    <div>
                        <span style="display: block; font-size: 11px; font-weight: 650; color: var(--t-tertiary, #64748b); text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 2px;">Academic Department</span>
                        <strong style="color: var(--t-primary, #0f172a); font-size: 15px;">{{ session('teacher_dept', 'Elementary / High School') }}</strong>
                    </div>
                </div>
            </div>

            {{-- Change Password Form --}}
            <div style="background: var(--s-surface, #ffffff); border: 1px solid var(--s-border, #e2e8f0); border-radius: var(--r, 12px); padding: 24px; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; border-bottom: 1px solid var(--s-border, #e2e8f0); padding-bottom: 16px;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: #fef3c7; color: #d97706;">
                        <i data-lucide="key-round" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: var(--t-primary, #0f172a);">Security & Password</h3>
                        <p style="margin: 2px 0 0; font-size: 12px; color: var(--t-tertiary, #64748b);">Change your local database login credentials.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('teacher.settings.password') }}" class="teacher-form">
                    @csrf
                    
                    <label>
                        <span>Current Password</span>
                        <input type="password" name="current_password" required>
                        @error('current_password')
                            <span style="color: #e11d48; font-size: 11px; font-weight: 600; margin-top: 2px;">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>
                        <span>New Password (min 8 characters)</span>
                        <input type="password" name="new_password" required>
                        @error('new_password')
                            <span style="color: #e11d48; font-size: 11px; font-weight: 600; margin-top: 2px;">{{ $message }}</span>
                        @enderror
                    </label>

                    <label>
                        <span>Confirm New Password</span>
                        <input type="password" name="new_password_confirmation" required>
                    </label>

                    <button type="submit" class="teacher-primary-btn" style="align-self: flex-start; margin-top: 6px;">
                        <i data-lucide="check-check"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: Microsoft Integration --}}
        <div style="background: var(--s-surface, #ffffff); border: 1px solid var(--s-border, #e2e8f0); border-radius: var(--r, 12px); padding: 24px; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px; border-bottom: 1px solid var(--s-border, #e2e8f0); padding-bottom: 16px;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: #e0e7ff; color: #4f46e5;">
                    <i data-lucide="microsoft" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: var(--t-primary, #0f172a);">Microsoft 365 Account</h3>
                    <p style="margin: 2px 0 0; font-size: 12px; color: var(--t-tertiary, #64748b);">Bind and sync your official Microsoft account.</p>
                </div>
            </div>

            @if($user->microsoft_id)
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 18px; display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="display: inline-flex; width: 10px; height: 10px; border-radius: 50%; background: #22c55e;"></span>
                        <strong style="color: #166534; font-size: 14px;">Linked & Connected</strong>
                    </div>
                    
                    <div style="font-size: 13px; color: #374151;">
                        <div style="margin-bottom: 6px;">
                            <span style="font-weight: 600; color: #166534;">Microsoft Account:</span> 
                            <span style="font-family: monospace; font-weight: 700;">{{ $user->microsoft_email }}</span>
                        </div>
                        <div>
                            <span style="font-weight: 600; color: #166534;">Connected On:</span> 
                            <span>{{ $user->microsoft_linked_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('teacher.settings.microsoft.disconnect') }}">
                    @csrf
                    <button type="submit" class="teacher-outline-btn" style="color: #e11d48; border-color: #fca5a5;" onclick="return confirm('Are you sure you want to disconnect your Microsoft 365 account?')">
                        <i data-lucide="unlink"></i> Disconnect Microsoft Account
                    </button>
                </form>
            @else
                <div style="background: var(--s-surface-hover, #f8fafc); border: 1px solid var(--s-border, #e2e8f0); border-radius: 8px; padding: 18px; display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="display: inline-flex; width: 10px; height: 10px; border-radius: 50%; background: #94a3b8;"></span>
                        <strong style="color: #475569; font-size: 14px;">Not Connected</strong>
                    </div>
                    <p style="margin: 0; font-size: 13px; line-height: 1.5; color: var(--t-secondary, #334155);">
                        Link your official Microsoft 365 education account to enable auto-provisioning of subject channels and classes.
                    </p>
                </div>

                <a href="{{ route('teacher.settings.microsoft.connect') }}" class="teacher-primary-btn" style="background: #2563eb; color: #fff;">
                    <i data-lucide="link"></i> Connect Microsoft Account
                </a>
            @endif

            <div style="margin-top: 24px; padding-top: 18px; border-top: 1px dashed var(--s-border, #e2e8f0); font-size: 12px; color: var(--t-tertiary, #64748b); line-height: 1.5;">
                <h4 style="margin: 0 0 6px; font-size: 12px; font-weight: 700; color: var(--t-secondary, #334155);">Why link Microsoft 365?</h4>
                <ul style="margin: 0; padding-left: 18px;">
                    <li>Enables Microsoft Teams virtual classrooms.</li>
                    <li>Synchronizes private channels per subject dynamically.</li>
                    <li>Prepares you to start online meetings and post welcome updates seamlessly.</li>
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection
