<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\JobPosting;

Schedule::call(function () {
    $today = now()->setTimezone('Europe/Lisbon')->startOfDay();

    JobPosting::where('status', 'Active')
                ->where('deadline', '<', $today)
                ->update(['status' => 'Expired']);

})->hourly()->timezone('Europe/Lisbon');