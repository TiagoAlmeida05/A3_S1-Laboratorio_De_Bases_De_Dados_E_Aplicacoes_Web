<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceEntry extends Model
{
    use HasFactory;

    protected $table = 'experience_entry';
    public $timestamps = false; 
    
    protected $fillable = [
        'position_name',
        'employer',
        'start_date',
        'end_date',
        'description',
        'job_seeker_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'job_seeker_id');
    }
}
