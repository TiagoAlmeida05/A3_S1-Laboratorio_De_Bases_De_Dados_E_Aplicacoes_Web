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

        DB::table('bookmark_notification')->insert([
            'notification_id' => $notifId,
            'job_seeker_id' => $item->job_seeker_id,
            'job_posting_id' => $item->job_posting_id
        ]);
        event(new PlatformAlert($message, $item->job_seeker_id, $notifId));
    }
})->daily()->timezone('Europe/Lisbon');