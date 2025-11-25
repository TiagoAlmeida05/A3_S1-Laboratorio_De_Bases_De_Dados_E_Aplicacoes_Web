<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobSeeker extends Model
{
    protected $table = 'job_seeker';
    protected $primaryKey = 'registered_user_id';
    public $incrementing = false;
    public $timestamps = false;

    public function registeredUser()
    {
        return $this->belongsTo(RegisteredUser::class, 'registered_user_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_seeker_id');
    }

    public function certifications()
    {
        return $this->hasMany(CertificationEntry::class, 'job_seeker_id');
    }

    public function experienceEntries()
    {
        return $this->hasMany(ExperienceEntry::class, 'job_seeker_id');
    }

    public function educationEntries()
    {
        return $this->hasMany(EducationEntry::class, 'job_seeker_id');
    }

    public function awards()
    {
        return $this->hasMany(AwardEntry::class, 'job_seeker_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'job_seeker_tag', 'job_seeker_id', 'tag_id');
    }

    public function socialMediaProfiles()
    {
        return $this->hasMany(SocialMediaProfile::class, 'job_seeker_id');
    }
}
