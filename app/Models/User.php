<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Import Eloquent relationship classes.
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Disable default created_at and updated_at timestamps for this model.
    protected $table = 'registered_user';
    public $timestamps  = false;
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * Only these fields may be filled using methods like create() or update().
     * This protects against mass-assignment vulnerabilities.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birthday',
        'age',
        'status',
        'sign_up_date'
    ];

    /**
     * The attributes that should be hidden when serializing the model
     * (e.g., to arrays or JSON).
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to a specific type.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // Ensures password is always hashed automatically when set.
            'password' => 'hashed',
            'birthday' => 'date',
            'sign_up_date' => 'date',
        ];
    }

    public function administrator(): HasOne
    {
        return $this->hasOne(Administrator::class, 'registered_user_id');
    }

    public function recruiter(): HasOne
    {
        return $this->hasOne(Recruiter::class, 'registered_user_id');
    }

    public function jobSeeker(): HasOne
    {
        return $this->hasOne(JobSeeker::class, 'registered_user_id');
    }

    //aux
    public function isAdmin(): bool
    {
        return $this->administrator()->exists();
    }

    public function isRecruiter(): bool
    {
        return $this->recruiter()->exists();
    }

    public function isJobSeeker(): bool
    {
        return $this->jobSeeker()->exists() && !$this->recruiter()->exists();
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
