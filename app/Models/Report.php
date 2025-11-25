<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Nome da tabela no teu SQL
    protected $table = 'report_to_admin';
    
    // O SQL não tem created_at/updated_at
    public $timestamps = false;

    protected $fillable = ['date', 'description', 'solved', 'reporter_id', 'handled_by_id'];

    protected $casts = [
        'date' => 'datetime',
        'solved' => 'boolean'
    ];

    // Relação: Quem fez a denúncia?
    public function reporter() {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}