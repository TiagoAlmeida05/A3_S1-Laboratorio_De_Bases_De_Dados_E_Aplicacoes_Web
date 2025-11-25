<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;

class Recruiter extends Model
{
    use HasFactory;

    protected $table = 'recruiter';
    protected $primaryKey = 'registered_user_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'registered_user_id',
        'is_company_manager',
        'department_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'registered_user_id');
    }

    public function department() 
    {
         return $this->belongsTo(Department::class, 'department_id');
    }

    public function job_postings() {
        return $this->hasMany(JobPosting::class, 'recruiter_id');
    }

}