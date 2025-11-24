<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Relations\HasMany;

class RegisteredUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'registered_user';
    public $timestamps = false;


    protected $fillable = [
        'name',
        'email',
        'password',
        'birthday',
        'age',
        'sign_up_date',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'birthday' => 'date',
        'sign_up_date' => 'date',
    ];

    public function jobSeeker()
    {
        return $this->hasOne(JobSeeker::class, 'registered_user_id');
    }

    public function administrator()
    {
        return $this->hasOne(Administrator::class, 'registered_user_id');
    }

    public function recruiter()
    {
        return $this->hasOne(Recruiter::class, 'registered_user_id');
    }
}
