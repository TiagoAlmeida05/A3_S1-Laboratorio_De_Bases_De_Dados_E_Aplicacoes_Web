<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPosting;

class AdminController extends Controller
{
    // US56: Gerir Ofertas de Emprego
    public function manageJobs() {
        $jobs = JobPosting::orderBy('id', 'asc')->get();
        return view('admin.jobs', ['jobs' => $jobs]);
    }

    public function approveJob($id) {
        $job = JobPosting::findOrFail($id);
        $job->status = 'Active';
        $job->save();
        return redirect()->route('admin.jobs')->with('success', 'Oferta aprovada!');
    }

    public function deleteJob($id) {
        $job = JobPosting::findOrFail($id);
        $job->delete();
        return redirect()->route('admin.jobs')->with('success', 'Oferta removida!');
    }
}