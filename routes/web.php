<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $resumesCount = $user->resumes()->count();
    $latestResume = $user->resumes()->first();
    $availableShortcuts = $user->is_admin ? ['notes', 'calendar', 'profile'] : ['resumes', 'notes', 'calendar', 'profile'];
    $defaultShortcuts = ['notes', 'calendar'];
    $dashboardShortcuts = $user->dashboard_shortcuts ?? $defaultShortcuts;

    return Inertia::render('Dashboard', [
        'resumesCount' => $resumesCount,
        'latestResume' => $latestResume ? [
            'id' => $latestResume->id,
            'title' => $latestResume->title,
            'updated_at_formatted' => $latestResume->updated_at->diffForHumans(),
            'updated_at_full' => $latestResume->updated_at->format('d/m/Y H:i'),
        ] : null,
        'notesCount' => $user->notes()->count(),
        'upcomingEventsCount' => $user->calendarEvents()->where('starts_at', '>=', now())->count(),
        'shortcuts' => array_values(array_intersect($dashboardShortcuts, $availableShortcuts)),
        'availableShortcuts' => $availableShortcuts,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Professional -> Resumes
    Route::get('/profissional/curriculos', [ResumeController::class, 'index'])->name('resumes.index');
    Route::post('/profissional/curriculos', [ResumeController::class, 'store'])->name('resumes.store');
    Route::get('/profissional/curriculos/{resume}', [ResumeController::class, 'show'])->name('resumes.show');
    Route::get('/profissional/curriculos/{resume}/edit', [ResumeController::class, 'edit'])->name('resumes.edit');
    Route::put('/profissional/curriculos/{resume}', [ResumeController::class, 'update'])->name('resumes.update');
    Route::delete('/profissional/curriculos/{resume}', [ResumeController::class, 'destroy'])->name('resumes.destroy');
    Route::post('/profissional/curriculos/{resume}/duplicate', [ResumeController::class, 'duplicate'])->name('resumes.duplicate');
    Route::get('/profissional/curriculos/{resume}/pdf', [ResumeController::class, 'pdf'])->name('resumes.pdf');

    Route::get('/anotacoes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('/anotacoes', [NoteController::class, 'store'])->name('notes.store');
    Route::put('/anotacoes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/anotacoes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
    Route::delete('/anotacoes-tags/{tag}', [NoteController::class, 'destroyTag'])->name('notes.tags.destroy');

    Route::get('/agenda', [CalendarEventController::class, 'index'])->name('calendar.index');
    Route::post('/agenda', [CalendarEventController::class, 'store'])->name('calendar.store');
    Route::put('/agenda/{calendarEvent}', [CalendarEventController::class, 'update'])->name('calendar.update');
    Route::delete('/agenda/{calendarEvent}', [CalendarEventController::class, 'destroy'])->name('calendar.destroy');

    Route::get('/configuracoes/integracoes', [IntegrationController::class, 'index'])->name('integrations.index');
    Route::get('/configuracoes/integracoes/google', [IntegrationController::class, 'connect'])->name('integrations.google.connect');
    Route::get('/configuracoes/integracoes/google/callback', [IntegrationController::class, 'callback'])->name('integrations.google.callback');
    Route::post('/configuracoes/integracoes/google/sync', [IntegrationController::class, 'sync'])->name('integrations.google.sync');
    Route::delete('/configuracoes/integracoes/google', [IntegrationController::class, 'disconnect'])->name('integrations.google.disconnect');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/dashboard/atalhos', [ProfileController::class, 'shortcuts'])->name('dashboard.shortcuts');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::patch('/atalhos', [AdminController::class, 'shortcuts'])->name('shortcuts');
        Route::get('/usuarios', [AdminController::class, 'users'])->name('users');
        Route::patch('/usuarios/{user}/administrador', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
        Route::get('/integracoes', [AdminController::class, 'integrations'])->name('integrations');
        Route::patch('/integracoes/google', [AdminController::class, 'toggleGoogle'])->name('integrations.google.toggle');
        Route::patch('/integracoes/google-login', [AdminController::class, 'toggleGoogleLogin'])->name('integrations.google-login.toggle');
        Route::patch('/integracoes/google/credenciais', [AdminController::class, 'updateGoogleCredentials'])->name('integrations.google.credentials');
    });
});

require __DIR__.'/auth.php';
