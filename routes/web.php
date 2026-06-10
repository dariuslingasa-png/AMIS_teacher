<?php

use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\TeacherPortalController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [TeacherAuthController::class, 'showLogin'])->name('teacher.login');
    Route::post('/login', [TeacherAuthController::class, 'login'])->name('teacher.login.store');
    Route::post('/login/change-password', [TeacherAuthController::class, 'changePassword'])->name('teacher.login.change-password.store');
    Route::get('/auth/microsoft/teacher', [TeacherAuthController::class, 'redirectMicrosoft'])->name('teacher.login.microsoft.redirect');
    Route::get('/auth/microsoft/teacher/callback', [TeacherAuthController::class, 'callbackMicrosoft'])->name('teacher.login.microsoft.callback');
});

Route::middleware('teacher')->group(function () {
    Route::post('/logout', [TeacherAuthController::class, 'logout'])->name('teacher.logout');

    Route::get('/dashboard', [TeacherPortalController::class, 'dashboard'])->name('teacher.dashboard');
    Route::get('/subjects', [TeacherPortalController::class, 'subjects'])->name('teacher.subjects');
    Route::post('/subjects', [TeacherPortalController::class, 'storeSubject'])->name('teacher.subjects.store');
    Route::get('/subjects/{subject}', [TeacherPortalController::class, 'subjectWorkspace'])->name('teacher.subjects.workspace');
    Route::post('/materials', [TeacherPortalController::class, 'storeMaterial'])->name('teacher.materials.store');

    Route::get('/meetings', [TeacherPortalController::class, 'meetings'])->name('teacher.meetings');
    Route::post('/meetings', [TeacherPortalController::class, 'storeMeeting'])->name('teacher.meetings.store');

    Route::get('/grades', [TeacherPortalController::class, 'grades'])->name('teacher.grades');
    Route::post('/grades/assessments', [TeacherPortalController::class, 'storeAssessment'])->name('teacher.assessments.store');
    Route::post('/grades/scores', [TeacherPortalController::class, 'storeScores'])->name('teacher.grades.scores.store');

    Route::get('/students', [TeacherPortalController::class, 'students'])->name('teacher.students');

    Route::get('/announcements', [TeacherPortalController::class, 'announcements'])->name('teacher.announcements');
    Route::post('/announcements', [TeacherPortalController::class, 'storeAnnouncement'])->name('teacher.announcements.store');

    // Settings & Microsoft Link
    Route::get('/settings', [TeacherPortalController::class, 'settings'])->name('teacher.settings');
    Route::post('/settings/password', [TeacherPortalController::class, 'updatePassword'])->name('teacher.settings.password');
    Route::get('/settings/microsoft/connect', [TeacherAuthController::class, 'connectMicrosoft'])->name('teacher.settings.microsoft.connect');
    Route::post('/settings/microsoft/disconnect', [TeacherAuthController::class, 'disconnectMicrosoft'])->name('teacher.settings.microsoft.disconnect');
});
