<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use App\Models\WebsiteContent;

class AdminController extends Controller
{
    // US56:
    public function manageJobs() {
        $jobs = JobPosting::orderBy('id', 'asc')->paginate(4);
        return view('admin.jobs', ['jobs' => $jobs]);
    }

    public function deleteJob($id) {
        $job = JobPosting::findOrFail($id);
        $job->delete();
        return redirect()->route('admin.jobs')->with('success', 'Job Posting removed!');
    }

    //US57
    public function manageContent() {
        $reports = Report::orderBy('solved', 'asc')->orderBy('id', 'asc')->paginate(4);
        
        return view('admin.content', ['reports' => $reports]);
    }

    public function solveReport($id) {
        $report = Report::findOrFail($id);
        
        $report->solved = true;
        $report->handled_by_id = Auth::id();
        
        $report->save();

        return redirect()->route('admin.content')->with('Report marked as resolved!');
    }

    public function reopenReport($id) {
        $report = Report::findOrFail($id);
        $report->solved = false;
        $report->handled_by_id = null;
        $report->save();

        return redirect()->route('admin.content')->with('success', 'Report reopened.');
    }

    //US58
    public function editPages() {
        $pages = WebsiteContent::orderBy('id', 'asc')->get();
        return view('admin.pages.index', ['pages' => $pages]);
    }

    public function showPageForm($id) {
        $page = WebsiteContent::findOrFail($id);
        return view('admin.pages.edit', ['page' => $page]);
    }

    public function updatePage(Request $request, $id) {
        $page = WebsiteContent::findOrFail($id);
        
        $request->validate([
            'headings' => 'array',
            'headings.*' => 'nullable|string',
            'texts' => 'array',
            'texts.*' => 'nullable|string',
        ]);

        $contentBlocks = [];
        $headings = $request->input('headings', []);
        $texts = $request->input('texts', []);

        if (!empty($headings)) {
            foreach ($headings as $index => $heading) {
                $text = $texts[$index] ?? '';
                
                if (!empty(trim($heading)) || !empty(trim($text))) {
                    $contentBlocks[] = [
                        'heading' => $heading,
                        'text' => $text
                    ];
                }
            }
        }

        $page->content = json_encode($contentBlocks);;
        $page->last_edited_by = Auth::id();
        
        $page->save();

        return redirect()->route('admin.pages')->with('success', "{$page->name} updated successfully!");
    }

    
}