<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Article extends Model
{
    protected $primaryKey = 'ID_article'; // Primary Key

    protected $fillable = [
        'ID_user',
        'title',
        'category_content',
        'content',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'ID_user');
    }
}
