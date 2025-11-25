<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AwardEntry extends Model
{
    use HasFactory;

    protected $table = 'award_entry';
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'issued_by',
        'date',
        'description',
        'job_seeker_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'job_seeker_id');
    }
}
