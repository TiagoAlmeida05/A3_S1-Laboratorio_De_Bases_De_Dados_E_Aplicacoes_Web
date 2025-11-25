<?php

use Illuminate\Support\Facades\Route;

// Imports Gerais
use App\Http\Controllers\ItemController;

// Imports de Autenticação
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

// Imports do Projeto (Juntei os teus e os dela)
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\AdminController;      // <--- Teu (US56)
use App\Http\Controllers\JobSeekerController;  // <--- Dela (US19)

// Home
Route::redirect('/', '/login');

/*
// Cards (boilerplate - podes manter ou apagar)
Route::middleware('auth')->controller(CardController::class)->group(function () {
    Route::get('/cards', 'index')->name('cards.index');
    Route::get('/cards/{card}', 'show')->name('cards.show');
});
*/

// Boilerplate do ItemController (vinha no template)
Route::middleware('auth')->controller(ItemController::class)->group(function () {
    Route::post('/api/cards/{card}/items', 'store');
    Route::patch('/api/items/{item}', 'update');
    Route::delete('/api/items/{item}', 'destroy');
});


// --- AUTENTICAÇÃO ---

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
});

// Usei a tua versão do Logout (POST e GET) porque é mais segura
Route::controller(LogoutController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/logout', 'logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});


// --- ÁREA DE UTILIZADOR AUTENTICADO (US01, US02, US03, US19) ---

Route::middleware('auth')->group(function () {
    
    // Job Postings (Comum aos dois)
    Route::controller(JobPostingController::class)->group(function () {
        Route::get('/job_postings', 'index')->name('job_postings.index');
        Route::get('/job_postings/{jobPosting}', 'show')->name('job_postings.show');
    });

    // Job Seeker Profile (Vindo da branch DELA - US19)
    Route::get('/job-seeker/{registered_user_id}', [JobSeekerController::class, 'show'])->name('jobseeker.profile');

});


// --- ÁREA DE ADMINISTRAÇÃO (Vindo da tua branch - US56) ---

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/jobs', [AdminController::class, 'manageJobs'])->name('admin.jobs'); 
    Route::patch('/admin/jobs/{id}/approve', [AdminController::class, 'approveJob'])->name('admin.jobs.approve');
    Route::delete('/admin/jobs/{id}', [AdminController::class, 'deleteJob'])->name('admin.jobs.delete');
    
    // Podes adicionar aqui as rotas US57 e US58 se já as tiveres feito
    Route::get('/admin/content', [AdminController::class, 'manageContent'])->name('admin.content');
    Route::patch('/admin/content/{id}/solve', [AdminController::class, 'solveReport'])->name('admin.reports.solve');
    Route::get('/admin/pages', [AdminController::class, 'editPages'])->name('admin.pages');
});