<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $table = 'application';
    public $timestamps = false; 
    
    protected $fillable = [
        'date',
        'cover_letter',
        'recommendation_letter',
        'evaluated',
        'accepted',
        'job_seeker_id',
        'job_posting_id',
    ];

    protected $casts = [
        'date' => 'datetime',
        'evaluated' => 'boolean',
        'accepted' => 'boolean',
    ];

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'job_seeker_id');
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }
}
