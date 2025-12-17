<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';
    public $timestamps = false;
    protected $fillable = [
        'content',
        'notification_type_id',
        'registered_user-id',
        'issue_date',
        'read_date'
    ];
}
