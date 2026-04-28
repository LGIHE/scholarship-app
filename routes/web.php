<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Public pages
Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/resources', function () {
    return Inertia::render('Resources');
})->name('resources');

Route::get('/faq', function () {
    return Inertia::render('FAQ');
})->name('faq');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:5000',
    ]);

    // Here you would typically send an email or store the message
    // For now, we'll just return a success message
    
    return back()->with('status', 'Thank you for your message! We\'ll get back to you soon.');
})->name('contact.submit');

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AcademicProgressController;

Route::get('/portal', function () {
    $application = auth()->user()->applications()->latest()->first();
    return Inertia::render('Dashboard', [
        'application' => $application
    ]);
})->middleware(['auth', 'verified', \App\Http\Middleware\EnsureApplicantOrScholar::class])->name('portal');

Route::middleware(['auth', \App\Http\Middleware\EnsureApplicantOrScholar::class])->group(function () {
    Route::get('/portal/application', [ApplicationController::class, 'form'])->name('application.form');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Application endpoints
    Route::post('/application/draft', [ApplicationController::class, 'draft'])->name('application.draft');
    Route::post('/application/submit', [ApplicationController::class, 'submit'])->name('application.submit');
    
    // Academic Progress endpoints (for scholars)
    Route::get('/portal/academic-progress', [AcademicProgressController::class, 'index'])->name('academic-progress.index');
    Route::post('/portal/academic-progress', [AcademicProgressController::class, 'store'])->name('academic-progress.store');
    Route::patch('/portal/academic-progress/{academicProgress}', [AcademicProgressController::class, 'update'])->name('academic-progress.update');
    Route::delete('/portal/academic-progress/{academicProgress}', [AcademicProgressController::class, 'destroy'])->name('academic-progress.destroy');
});

require __DIR__.'/auth.php';
