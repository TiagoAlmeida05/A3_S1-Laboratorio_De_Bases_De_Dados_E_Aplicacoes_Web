<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model {
    protected $table = 'company';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'website',
        'logo',
        'about_us',
        'city_id'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function jobPostings()
    {
        return $this->hasManyThrough(
            JobPosting::class,   
            Department::class,   
            'company_id',        
            'recruiter_id',      
            'id',                
            'id'                 
        );
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'company_tag');
    }

    public function socialMediaProfiles()
    {
        return $this->hasMany(SocialMediaProfile::class);
    }
}