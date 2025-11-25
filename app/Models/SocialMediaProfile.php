<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaProfile extends Model
{
    use HasFactory;

    protected $table = 'social_media_profile';
    public $timestamps = false;

    protected $fillable = [
        'url',
        'social_media_type_id',
        'company_id',
        'job_seeker_id',
    ];

    public function socialMediaType(): BelongsTo
    {
        return $this->belongsTo(SocialMediaType::class, 'social_media_type_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'job_seeker_id');
    }
}
