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

    public function department() {
        return $this->hasMany(Department::class, 'company_id');
    }

    public function city() {
        return $this->belongsTo(City::class, 'city_id');
    }
}