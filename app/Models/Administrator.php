<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory;

    protected $table = 'administrator';

    protected $primaryKey = 'registered_user_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'registered_user_id',
    ];

    // Relação inversa: O Admin pertence a um User
    public function user()
    {
        return $this->belongsTo(User::class, 'registered_user_id');
    }
}