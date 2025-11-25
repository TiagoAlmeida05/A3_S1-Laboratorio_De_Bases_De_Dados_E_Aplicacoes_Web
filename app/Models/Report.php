<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'report_to_admin'; // O nome exato no SQL
    public $timestamps = false;

    protected $fillable = ['date', 'description', 'solved', 'reporter_id', 'handled_by_id'];

    protected $casts = [
        'date' => 'datetime',
        'solved' => 'boolean'
    ];

    // Quem fez a denúncia?
    public function reporter() {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    // Quem resolveu? (Admin)
    public function admin() {
        return $this->belongsTo(Administrator::class, 'handled_by_id', 'registered_user_id');
    }
}