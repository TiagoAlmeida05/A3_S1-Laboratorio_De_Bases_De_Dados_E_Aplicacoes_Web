<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialMediaType extends Model
{
    use HasFactory;

    protected $table = 'social_media_type';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'default_url',
        'illustration',
    ];

    public function socialMediaProfiles(): HasMany
    {
        return $this->hasMany(SocialMediaProfile::class, 'social_media_type_id');
    }
}
