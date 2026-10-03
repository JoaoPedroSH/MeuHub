<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumeController;
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

    return Inertia::render('Dashboard', [
        'resumesCount' => $resumesCount,
        'latestResume' => $latestResume ? [
            'id' => $latestResume->id,
            'title' => $latestResume->title,
            'updated_at_formatted' => $latestResume->updated_at->diffForHumans(),
            'updated_at_full' => $latestResume->updated_at->format('d/m/Y H:i'),
        ] : null,
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

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
