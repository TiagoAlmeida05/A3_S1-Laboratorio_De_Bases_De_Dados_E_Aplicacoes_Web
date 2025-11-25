<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'company_id'
    ];

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }
}