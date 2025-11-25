<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tag';
    public $timestamps = false; 
    
    protected $fillable = [
        'name',
        'job_posting_exclusive',
    ];

    protected $casts = [
        'job_posting_exclusive' => 'boolean',
    ];

    public function jobSeekers(): BelongsToMany
    {
        return $this->belongsToMany(JobSeeker::class, 'job_seeker_tag', 'tag_id', 'job_seeker_id');
    }

    public function jobPostings(): BelongsToMany
    {
        return $this->belongsToMany(JobPosting::class, 'job_posting_tag', 'tag_id', 'job_posting_id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_tag', 'tag_id', 'company_id');
    }
}
