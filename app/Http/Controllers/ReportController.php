<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\JobSeeker;
use App\Models\JobPosting;
use App\Models\Company;
use App\Models\Application;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SubmitReportRequest;

use Illuminate\Http\Request;

class ReportController extends Controller {
    public function create(Request $request) {
        $type = $request->query('type', 'General'); // Ask for the type; the default is General
        $entityID = $request->query('entity_id');
        $entityName = null;

        if ($type === 'JobSeeker' && $entityID) {
            $entity = JobSeeker::with('registeredUser')->find($entityID);
            $entityName = $entity?->registeredUser?->name;
        } 
        elseif ($type === 'JobPosting' && $entityID) {
            $entity = JobPosting::find($entityID);
            $entityName = $entity?->title;
        } 
        elseif ($type === 'Company' && $entityID) {
            $entity = Company::find($entityID);
            $entityName = $entity?->name;
        } 
        elseif ($type === 'Application' && $entityID) {
            $entity = Application::with('jobSeeker.registeredUser', 'jobPosting')->find($entityID);
            $entityName = $entity ? $entity->jobSeeker->registeredUser->name . ' application to ' . $entity->jobPosting->title : null;
        }

        return view('reports.create-report', [
            'type' => $type,
            'entityID' => $entityID,
            'entityName' => $entityName
        ]);
    }

    public function store(SubmitReportRequest $request) {
        $data = [
            'description' => $request->input('description'),
            'reporter_id' => Auth::id(),
            'date' => now(),
            'solved' => false
        ];

        switch ($request->input('type')) {
            case 'JobSeeker':
                $data['reported_job_seeker_id'] = $request->input('entity_id');
                break;
            case 'JobPosting':
                $data['reported_job_posting_id'] = $request->input('entity_id');
                break;
            case 'Company':
                $data['reported_company_id'] = $request->input('entity_id');
                break;
            case 'Application':
                $data['reported_application_id'] = $request->input('entity_id');
                break;
        }

        Report::create($data);
        $message = "Report submitted successfully. It will now be reviewed by a HireUp administrator.\nThank you for your feedback!";

        return redirect()->back()->with('success', $message);
    }
}
