<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

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

    // US57: Ver Denúncias
    public function manageContent() {
        $reports = Report::orderBy('solved', 'asc')->orderBy('date', 'desc')->get();
        return view('admin.content', ['reports' => $reports]);
    }

    // US57: Resolver Denúncia
    public function solveReport($id) {
        $report = Report::findOrFail($id);
        
        $report->solved = true;
        $report->handled_by_id = Auth::id();
        
        $report->save();

        return redirect()->route('admin.content')->with('success', 'Denúncia marcada como resolvida!');
    }
}