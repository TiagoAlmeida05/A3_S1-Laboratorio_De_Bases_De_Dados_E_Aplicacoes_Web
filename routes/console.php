<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use App\Models\JobPosting;
use App\Events\PlatformAlert;
use Carbon\Carbon;

Schedule::call(function () {
    $today = now()->setTimezone('Europe/Lisbon')->startOfDay();

    JobPosting::where('status', 'Active')
                ->where('deadline', '<', $today)
                ->update(['status' => 'Expired']);

})->hourly()->timezone('Europe/Lisbon');

Schedule::call(function() {
    $targetDate = Carbon::now()->addDays(5)->toDateString();

    $bookmarks = DB::table('bookmark')
        ->join('job_posting', 'bookmark.job_posting_id', '=', 'job_posting.id')
        ->where('bookmark.is_active', true)
        ->where('job_posting.status', 'Active')
        ->whereDate('job_posting.deadline', $targetDate)
        ->select('bookmark.job_seeker_id', 'bookmark.job_posting_id', 'job_posting.title')
        ->get();

    foreach ($bookmarks as $item) {
        $message = "Reminder: The job '{$item->title}' closes in 5 days!";

        $notifId = DB::table('notification')->insertGetId([
            'content' => $message,
            'notification_type_id' => 3,
            'registered_user_id' => $item->job_seeker_id,
            'issue_date' => now(),
        ]);
        
        event(new PlatformAlert($message, $item->job_seeker_id, $notifId, 3));
    }

    $expiringJobs = DB::table('job_posting')
        ->where('status', 'Active')
        ->whereDate('deadline', $targetDate)
        ->select('id', 'title', 'recruiter_id')
        ->get();

    foreach($expiringJobs as $job) {
        $staffMessage = "Action Required: Your job posting '{$job->title}' expires in 5 days.";

        $recipients = collect([$job->recruiter_id]);

        $manager = DB::table('recruiter')
            ->join('department', 'recruiter.department_id', '=', 'department.id')
            ->where('recruiter.is_company_manager', true)
            ->where('department.company_id', function($query) use ($job) {
                $query->select('d2.company_id')
                      ->from('recruiter as r2')
                      ->join('department as d2', 'r2.department_id', '=', 'd2.id')
                      ->where('r2.registered_user_id', $job->recruiter_id);
            })
            ->select('recruiter.registered_user_id')
            ->first();
        
        if($manager && $manager->registered_user_id != $job->recruiter_id) {
            $recipients->push($manager->registered_user_id);
        }

        foreach($recipients->unique() as $userId){
            $notifId = DB::table('notification')->insertGetId([
                'content' => $staffMessage,
                'notification_type_id' => 1,
                'registered_user_id' => $userId,
                'issue_date' => now(),
            ]);

            event(new PlatformAlert($staffMessage, $userId, $notifId, 1));
        }
    }
})->daily()->timezone('Europe/Lisbon');