<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\CompleteRegistrationController;

use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JobSeekerController; 
use App\Http\Controllers\RecruiterController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;

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
    Route::get('/notifications/fetch', [NotificationController::class, 'getUserNotifications']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');    
    Route::post('/job-postings/{id}/bookmark', [BookmarkController::class, 'store'])    ->name('bookmarks.store');
    Route::delete('/job-postings/{id}/bookmark', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
    Route::get('/my-bookmarks', [BookmarkController::class, 'index'])->name('jobseeker.bookmarks');
    Route::get('/job-seeker/applications', [JobSeekerController::class, 'applications'])->name('jobseeker.applications');
    Route::get('/job-seeker/profile/edit', [JobSeekerController::class, 'edit'])->name('jobseeker.profile.edit');
    Route::get('/job-seeker/{registered_user_id}', [JobSeekerController::class, 'show'])->name('jobseeker.profile');
    Route::put('/job-seeker/profile/update', [JobSeekerController::class, 'update'])->name('jobseeker.profile.update');
    Route::get('/job_postings/{jobPosting}/apply', [JobSeekerController::class, 'applyForm'])->name('jobseeker.apply');
    Route::post('/job_postings/{jobPosting}/apply', [JobSeekerController::class, 'storeApplication'])->name('jobseeker.apply.store');
    Route::get('/applications/{application}/edit', [JobSeekerController::class, 'editApplication'])->name('applications.edit');
    Route::put('/applications/{application}', [JobSeekerController::class, 'updateApplication'])->name('applications.update');
    Route::delete('/applications/{application}/files/{fileType}', [JobSeekerController::class, 'deleteApplicationFile'])->name('applications.files.delete');
    Route::delete('/applications/{application}/cancel', [JobSeekerController::class, 'cancelApplication'])->name('applications.cancel');
    Route::delete('/jobseeker/profile', [JobSeekerController::class, 'destroy'])->name('jobseeker.profile.destroy');
    Route::get('/messages/{registered_user_id?}', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{registered_user_id}/json', [MessageController::class, 'messagesJson'])->name('messages.json');
    Route::get('/conversations/json', [App\Http\Controllers\MessageController::class, 'conversationsJson'])->name('conversations.json');
    Route::get('/settings/notifications', [NotificationController::class, 'settings'])->name('notifications.settings');
    Route::post('/settings/notifications', [NotificationController::class, 'updateSettings'])->name('notifications.settings.update');
    Route::get('/report', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/report', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/complete-registration', [CompleteRegistrationController::class, 'show'])->name('register.complete');
    Route::post('/complete-registration', [CompleteRegistrationController::class, 'store'])->name('register.complete.store');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/jobs', [AdminController::class, 'manageJobs'])->name('admin.jobs'); 
    Route::delete('/admin/jobs/{id}', [AdminController::class, 'deleteJob'])->name('admin.jobs.delete');
    Route::get('/admin/user_reports', [AdminController::class, 'manageUserReports'])->name('admin.user_reports');
    Route::patch('/admin/user_reports/{id}/solve', [AdminController::class, 'solveUserReport'])->name('admin.user_reports.solve');
    Route::get('/admin/pages', [AdminController::class, 'editPages'])->name('admin.pages');
    Route::get('/admin/pages/{id}/edit', [AdminController::class, 'showPageForm'])->name('admin.pages.edit');
    Route::put('/admin/pages/{id}', [AdminController::class, 'updatePage'])->name('admin.pages.update');
    Route::patch('/admin/users/{id}/block', [AdminController::class, 'blockUser'])->name('admin.users.block');
    Route::get('/admin/companies', [AdminController::class, 'manageCompanies'])->name('admin.companies');
    Route::get('/admin/companies/{id}/edit', [AdminController::class, 'editCompany'])->name('admin.companies.edit');
    Route::put('/admin/companies/{id}', [AdminController::class, 'updateCompany'])->name('admin.companies.update');
    Route::get('/admin/job-seekers', [AdminController::class, 'manageJobSeekers'])->name('admin.job_seekers');
    Route::get('/admin/job-seekers/{id}/edit', [AdminController::class, 'editJobSeeker'])->name('admin.job_seekers.edit');
    Route::put('/admin/job-seekers/{id}', [AdminController::class, 'updateJobSeeker'])->name('admin.job_seekers.update');
    Route::delete('/admin/job-seekers/{id}', [AdminController::class, 'deleteJobSeeker'])->name('admin.job_seeker.delete');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::delete('/admin/settings', [AdminController::class, 'destroy'])->name('admin.profile.destroy');
    Route::get('/notifications/send', [NotificationController::class, 'create'])->name('admin.notifications.create');
    Route::post('/notifications/send', [NotificationController::class, 'storeAdminNotification'])->name('admin.notifications.send');
    Route::get('/admin/users/create', [AdminController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/recruiters', [AdminController::class, 'manageRecruiters'])->name('admin.recruiters');
    Route::patch('/admin/recruiters/{id}/promote', [AdminController::class, 'promoteToManager'])->name('admin.recruiters.promote');
    Route::delete('/admin/recruiters/{id}', [AdminController::class, 'deleteRecruiter'])->name('admin.recruiters.delete');
    Route::get('/admin/applications/{application}', [JobPostingController::class, 'viewApplication'])->name('admin.view-application');
});

Route::get('/recruiter-dashboard', function () {
});

Route::middleware('user-role:recruiter')->controller(RecruiterController::class)->group(function () {
    Route::get('/recruiter-dashboard', 'index')->name('recruiter-dashboard.index');
    Route::get('/recruiter-dashboard/new-job-posting', [JobPostingController::class, 'create'])->name('job_postings.create');
    Route::get('/recruiter-dashboard/statistics', [RecruiterController::class, 'statistics'])->name('recruiter-dashboard.statistics');
    Route::delete('/recruiter/dashboard', [RecruiterController::class, 'destroy'])->name('recruiter.dashboard.destroy');
    Route::post('/job-postings', [JobPostingController::class, 'store'])->name('job_postings.store');
    Route::patch('/job-postings/{job_posting}/approve', [JobPostingController::class, 'approve'])->name('job_postings.approve');
    Route::get('/job-postings/{job_posting}/edit', [JobPostingController::class, 'edit'])->name('job_postings.edit');
    Route::put('/job-postings/{job_posting}', [JobPostingController::class, 'update'])->name('job_postings.update');
    Route::get('/job-postings/{job_posting}/select-applicants', [JobPostingController::class, 'selectApplicants'])->name('job_postings.select-applicants');
    Route::get('/job-postings/{job_posting}/applications', [JobPostingController::class, 'manageApplications'])->name('job_postings.manage-applications');
    Route::get('/applications/{application}', [JobPostingController::class, 'viewApplication'])->name('applications.view-application');
    Route::get('/job-postings/{job_posting}/view-applications-closed-job', [JobPostingController::class, 'viewApplicationsOfClosedJobs'])->name('job_postings.view-applications-closed-job');
    Route::get('/applications/{application}/closed-job', [JobPostingController::class, 'viewApplicationOfClosedJob'])->name('job_postings.view-application-closed-job');    Route::post('/job-postings/{job_posting}/applications/submit-application-selection', [JobPostingController::class, 'submitApplicationSelection'])->name('job_postings.submit-application-selection');
    Route::patch('/job-postings/{job_posting}/close', [JobPostingController::class, 'close'])->name('job_postings.close');
    Route::delete('/job-postings/{job_posting}', [JobPostingController::class, 'delete'])->name('job_postings.delete');
    Route::post('/recruiter/staff/promote', 'promoteToRecruiter')->name('recruiter.promote');
    Route::delete('/recruiter/staff/{id}/demote', 'demoteToJobSeeker')->name('recruiter.demote');
});

Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit')->middleware('auth');
Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update')->middleware('auth');

Route::get('/about-us', [PageController::class, 'about'])->name('page.about');
Route::get('/terms-of-service', [PageController::class, 'terms'])->name('page.terms');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('page.privacy');
Route::get('/faq', [PageController::class, 'faq'])->name('page.faq');
Route::get('/contact-us', [PageController::class, 'contacts'])->name('page.contacts');

Route::post('send-notification', [NotificationController::class, 'sendGeneralNotification']);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);