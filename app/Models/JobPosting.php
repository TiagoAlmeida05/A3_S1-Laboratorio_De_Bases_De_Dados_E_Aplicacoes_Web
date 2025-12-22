<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JobPosting extends Model {
    protected $table = 'job_posting';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'creation_date',
        'deadline',
        'min_wage',
        'max_wage',
        'requirements',
        'status',
        'recruiter_id',
        'city_id'
    ];

    public function city(): BelongsTo {
        return $this->belongsTo(City::class);
    }

    protected $casts = [
        'creation_date' => 'date',
        'deadline' => 'date',
    ];

    public function recruiter(): BelongsTo {
        return $this->belongsTo(Recruiter::class, 'recruiter_id', 'registered_user_id');
    }

    public function company() {
        return $this->hasOneThrough(Company::class, Recruiter::class,
            'registered_user_id',
            'id',
            'recruiter_id',
            'department_id'
        )->join('department', 'department.company_id', '=', 'company.id');
    }

    public function department(): HasOneThrough {
        return $this->hasOneThrough(Department::class, Recruiter::class,
            'registered_user_id',
            'id',
            'recruiter_id',
            'department_id'
        );
    }

    public function applications() {
        return $this->hasMany(Application::class, 'job_posting_id');
    }

    public function tags(): BelongsToMany {
        return $this->belongsToMany(Tag::class, 'job_posting_tag');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->whereHas('recruiter.department', function ($q) use ($companyId){
            $q->where('company_id', $companyId);
        });
    }
    public function bookmarkedBy()
    {
        return $this->belongsToMany(
            JobSeeker::class,
            'bookmark',
            'job_posting_id',
            'job_seeker_id'
        )
        ->withPivot(['date_added', 'is_active']);
    }

    public function getCompanyAttribute()
    {
        return $this->recruiter?->department?->company;
    }

}
