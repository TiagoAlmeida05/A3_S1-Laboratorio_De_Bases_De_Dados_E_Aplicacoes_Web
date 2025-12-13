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

    public function applications() {
        return $this->hasMany(Application::class, 'job_posting_id');
    }
}
