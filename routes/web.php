<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\JobSeekerController;

// Home
Route::get('/', [JobPostingController::class, 'index'])->name('homepage');

Route::get('/job_postings', function() {
    return redirect('/');
});

// Cards (authentication required)
Route::middleware('auth')->controller(CardController::class)->group(function () {
    Route::get('/cards', 'index')->name('cards.index');
    Route::get('/cards/{card}', 'show')->name('cards.show');
});


// API (authentication required)
Route::middleware('auth')->controller(CardController::class)->group(function () {
    Route::post('/api/cards', 'store');              // create card
    Route::delete('/api/cards/{card}', 'destroy');   // delete card
});

Route::middleware('auth')->controller(ItemController::class)->group(function () {
    Route::post('/api/cards/{card}/items', 'store'); // add item to card
    Route::patch('/api/items/{item}', 'update');     // update item
    Route::delete('/api/items/{item}', 'destroy');   // delete item
});


// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
});

Route::controller(LogoutController::class)->group(function () {
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// JobPosting (authentication required) ---> based on Cards!!
Route::middleware('auth')->controller(JobPostingController::class)->group(function () {
    Route::get('/job_postings', 'index')->name('job_postings.index');
    Route::get('/job_postings/{jobPosting}', 'show')->name('job_postings.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/job-seeker/{registered_user_id}', [JobSeekerController::class, 'show'])->name('jobseeker.profile');
});