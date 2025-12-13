<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model {
    protected $table = 'company';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'website',
        'logo',
        'about_us',
        'city_id'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function departments() {
        return $this->hasMany(Department::class, 'company_id', 'id');
    }

    public function jobPostings()
    {
        return $this->hasManyThrough(
            JobPosting::class,   
            Department::class,   
            'company_id',        
            'recruiter_id',      
            'id',                
            'id'                 
        );
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'company_tag');
    }

    public function socialMediaProfiles()
    {
        return $this->hasMany(SocialMediaProfile::class);
    }

    
    public function getCompanyJobPostings() {
        $departmentIDs = $this->departments()->pluck('id');
        
        return JobPosting::whereHas('recruiter', function($query) use ($departmentIDs) {
            $query->whereIn('department_id', $departmentIDs);
        });
    }

    public function getCompanyStatistics() {
        $companyJobPostings = $this->getCompanyJobPostings();
        $companyJobPostingIDs = $companyJobPostings->pluck('id');
        
        $applications = Application::whereIn('job_posting_id', $companyJobPostingIDs)->get();
        
        return [
            'total_company_job_postings' => $companyJobPostings->count(),
            'total_company_applications' => $applications->count(),
            'total_company_accepted_applications' => $applications->where('accepted', true)->count(),
        ];
    }
}