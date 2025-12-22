<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model {
    use HasFactory;

    protected $table = 'report_to_admin';
    public $timestamps = false;

    protected $fillable = [
        'date', 
        'description', 
        'solved', 
        'reporter_id', 
        'handled_by_id',
        'reported_job_seeker_id',
        'reported_job_posting_id',
        'reported_company_id',
        'reported_application_id'
    ];

    protected $casts = [
        'date' => 'datetime',
        'solved' => 'boolean'
    ];

    public function reporter() {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function handler() {
        return $this->belongsTo(User::class, 'handled_by_id');
    }

    public function reportedJobSeeker() {
        return $this->belongsTo(JobSeeker::class, 'reported_job_seeker_id', 'registered_user_id');
    }

    public function reportedJobPosting() {
        return $this->belongsTo(JobPosting::class, 'reported_job_posting_id');
    }

    public function reportedCompany() {
        return $this->belongsTo(Company::class, 'reported_company_id');
    }

    public function reportedApplication() {
        return $this->belongsTo(Application::class, 'reported_application_id');
    }

    public function getReportType() {
        if ($this->reported_job_seeker_id) return 'JobSeeker';
        if ($this->reported_job_posting_id) return 'JobPosting';
        if ($this->reported_company_id) return 'Company';
        if ($this->reported_application_id) return 'Application';
        return 'General';
    }

    public function getReportedContentID() {
        if ($this->reported_job_seeker_id) return $this->reportedJobSeeker;
        if ($this->reported_job_posting_id) return $this->reportedJobPosting;
        if ($this->reported_company_id) return $this->reportedCompany;
        if ($this->reported_application_id) return $this->reportedApplication;
        return null;
    }

    public function getReportTypeLabel() {
        if ($this->reported_job_seeker_id) return 'Job Seeker';
        if ($this->reported_job_posting_id) return 'Job posting';
        if ($this->reported_company_id) return 'Company';
        if ($this->reported_application_id) return 'Application';
        return 'General';
    }
}