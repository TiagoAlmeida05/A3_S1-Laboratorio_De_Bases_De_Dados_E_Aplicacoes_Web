<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSeeker extends Model
{
    use HasFactory;

    protected $table = 'job_seeker';
    protected $primaryKey = 'registered_user_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'registered_user_id',
        'profile_photo',
        'about_me',
        'website',
        'cv',
        'show_cv',
        'city_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'registered_user_id');
    }
}