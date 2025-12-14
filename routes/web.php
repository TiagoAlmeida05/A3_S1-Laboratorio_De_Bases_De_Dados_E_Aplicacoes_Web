<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JobSeekerController; 
use App\Http\Controllers\RecruiterController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PageController;

Route::get('/', [JobPostingController::class, 'index'])->name('homepage');

Route::get('/job_postings', function() {
    return redirect('/');
});

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
});

Route::controller(LogoutController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/logout', 'logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

Route::middleware('auth')->group(function () {
    Route::controller(JobPostingController::class)->group(function () {
        Route::get('/job_postings', 'index')->name('job_postings.index');
        Route::get('/job_postings/{jobPosting}', 'show')->name('job_postings.show');
    });

    Route::get('/job-seeker/{registered_user_id}', [JobSeekerController::class, 'show'])->name('jobseeker.profile');
    Route::get('/job-seeker/profile/edit', [JobSeekerController::class, 'edit'])->name('jobseeker.profile.edit');
    Route::put('/job-seeker/profile/update', [JobSeekerController::class, 'update'])->name('jobseeker.profile.update');
    Route::get('/job_postings/{jobPosting}/apply', [JobSeekerController::class, 'applyForm'])->name('jobseeker.apply');
    Route::post('/job_postings/{jobPosting}/apply', [JobSeekerController::class, 'storeApplication'])->name('jobseeker.apply.store');

});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/jobs', [AdminController::class, 'manageJobs'])->name('admin.jobs'); 
    Route::delete('/admin/jobs/{id}', [AdminController::class, 'deleteJob'])->name('admin.jobs.delete');
    Route::get('/admin/content', [AdminController::class, 'manageContent'])->name('admin.content');
    Route::patch('/admin/content/{id}/solve', [AdminController::class, 'solveReport'])->name('admin.reports.solve');
    Route::get('/admin/pages', [AdminController::class, 'editPages'])->name('admin.pages');
    Route::patch('/admin/content/{id}/reopen', [AdminController::class, 'reopenReport'])->name('admin.reports.reopen');
    Route::get('/admin/pages/{id}/edit', [AdminController::class, 'showPageForm'])->name('admin.pages.edit');
    Route::put('/admin/pages/{id}', [AdminController::class, 'updatePage'])->name('admin.pages.update');
    Route::get('/admin/users', [AdminController::class, 'manageUsers'])->name('admin.users');
    Route::patch('/admin/users/{id}/block', [AdminController::class, 'blockUser'])->name('admin.users.block');
    Route::get('/admin/companies', [AdminController::class, 'manageCompanies'])->name('admin.companies');
    Route::get('/admin/companies/{id}/edit', [AdminController::class, 'editCompany'])->name('admin.companies.edit');
    Route::put('/admin/companies/{id}', [AdminController::class, 'updateCompany'])->name('admin.companies.update');
});

Route::get('/recruiter-dashboard', function () {
});

Route::middleware('user-role:recruiter')->controller(RecruiterController::class)->group(function () {
    Route::get('/recruiter-dashboard', 'index')->name('recruiter-dashboard.index');
    Route::get('/recruiter-dashboard/new-job-posting', [JobPostingController::class, 'create'])->name('job_postings.create');
    Route::get('/recruiter-dashboard/statistics', [RecruiterController::class, 'statistics'])->name('recruiter-dashboard.statistics');
    Route::post('/job-postings', [JobPostingController::class, 'store'])->name('job_postings.store');
    Route::get('/job-postings/{job_posting}/edit', [JobPostingController::class, 'edit'])->name('job_postings.edit');
    Route::put('/job-postings/{job_posting}', [JobPostingController::class, 'update'])->name('job_postings.update');
    Route::get('/job-postings/{job_posting}/select-applicants', [JobPostingController::class, 'selectApplicants'])->name('job_postings.select-applicants');
    Route::get('/job-postings/{job_posting}/applications', [JobPostingController::class, 'manageApplications'])->name('job_postings.manage-applications');
    Route::get('/applications/{application}', [JobPostingController::class, 'viewApplication'])->name('applications.view-application');
    Route::get('/job-postings/{job_posting}/view-applications-closed-job', [JobPostingController::class, 'viewApplicationsOfClosedJobs'])->name('job_postings.view-applications-closed-job');
    Route::get('/applications/{application}/closed-job', [JobPostingController::class, 'viewApplicationOfClosedJob'])->name('job_postings.view-application-closed-job');    Route::post('/job-postings/{job_posting}/applications/submit-application-selection', [JobPostingController::class, 'submitApplicationSelection'])->name('job_postings.submit-application-selection');
    Route::patch('/job-postings/{job_posting}/close', [JobPostingController::class, 'close'])->name('job_postings.close');
    Route::delete('/job-postings/{job_posting}', [JobPostingController::class, 'delete'])->name('job_postings.delete');
});

Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit')->middleware('auth');
Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update')->middleware('auth');

Route::get('/about-us', [PageController::class, 'about'])->name('page.about');
Route::get('/terms-of-service', [PageController::class, 'terms'])->name('page.terms');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('page.privacy');
Route::get('/faq', [PageController::class, 'faq'])->name('page.faq');