<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationEntry extends Model
{
    use HasFactory;

    protected $table = 'education_entry';
    public $timestamps = false; 

    protected $fillable = [
        'name',
        'issued_by',
        'start_date',
        'end_date',
        'additional_info',
        'job_seeker_id',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'job_seeker_id');
    }
}
