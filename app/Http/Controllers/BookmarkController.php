<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\JobPosting;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function store($jobPostingId)
    {
        $jobSeekerId = Auth::id();

        $bookmark = \DB::table('bookmark')->where('job_seeker_id', $jobSeekerId)->where('job_posting_id', $jobPostingId)->first();

        if ($bookmark) {
            \DB::table('bookmark')->where('job_seeker_id', $jobSeekerId)->where('job_posting_id', $jobPostingId)->update(['is_active' => true]);
        } else {
            \DB::table('bookmark')->insert([
                'job_seeker_id' => $jobSeekerId,
                'job_posting_id' => $jobPostingId,
                'date_added' => now(),
                'is_active' => true,
            ]);
        }

        return back()->with('success', 'Job bookmarked!');
    }

    public function destroy($jobPostingId)
    {
        \DB::table('bookmark')->where('job_seeker_id', Auth::id())->where('job_posting_id', $jobPostingId)->update(['is_active' => false]);
        return back()->with('success', 'Bookmark removed.');
    }

    public function index()
    {
        $jobSeeker = Auth::user()->isJobSeeker();
        $bookmarks = $jobSeeker->bookmarks()->wherePivot('is_active', true)->with(['company', 'city'])->orderByPivot('date_added', 'desc')->paginate(10);
        return view('jobseeker.bookmarks', compact('bookmarks'));
    }
}
