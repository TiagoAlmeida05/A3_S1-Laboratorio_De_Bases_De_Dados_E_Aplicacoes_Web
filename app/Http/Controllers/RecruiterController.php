<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class RecruiterController extends Controller {
    public function index(): View {
        // DO LATER: Add logic to fetch recruiter dashboard stuff --> active job postings and inactive job postings (I think)
        $user = Auth::user();
<<<<<<< HEAD
        return view('recruiter.dashboard', [
            'user' => $user
=======
        $recruiter = $user->recruiter;
        $job_postings = $recruiter->job_postings()->orderBy('creation_date', 'desc')->get();

        return view('recruiter.dashboard', [
            'user' => $user,
            'job_postings' => $job_postings
>>>>>>> main
        ]);
    }
}
