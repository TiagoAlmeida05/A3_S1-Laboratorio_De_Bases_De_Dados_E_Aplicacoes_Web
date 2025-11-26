<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model {
    protected $table = 'city'; 
    public $timestamps = false;

    protected $fillable = [
        'name',
        'country_id'
    ];
    
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function jobSeekers()
    {
        return $this->hasMany(JobSeeker::class);
    }

    public function jobPostings()
    {
        return $this->hasMany(JobPosting::class);
    }
}