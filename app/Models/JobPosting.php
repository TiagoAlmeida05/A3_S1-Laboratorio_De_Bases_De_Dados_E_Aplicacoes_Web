<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPosting extends Model {
    protected $table = 'job_posting';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'deadline',
        'min_wage',
        'max_wage',
        'requirements',
        'city_id'
    ];

    public function city(): BelongsTo {
        return $this->belongsTo(City::class);
    }
    public function recruiter()
    {
        return $this->belongsTo(Recruiter::class, 'recruiter_id', 'registered_user_id');
    }
}
