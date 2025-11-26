<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteContent extends Model
{
    use HasFactory;

    protected $table = 'website_content'; // Nome exato no SQL
    public $timestamps = false;

    protected $fillable = ['name', 'content', 'last_edited_by'];

    // Relação: Quem editou por último?
    public function editor() {
        return $this->belongsTo(User::class, 'last_edited_by');
    }
}