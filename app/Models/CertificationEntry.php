<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificationEntry extends Model
{
    use HasFactory;

    protected $table = 'certification_entry';
    public $timestamps = false; 
    
    protected $fillable = [
        'name',
        'issued_by',
        'date',
        'description',
        'certificate_url',
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
