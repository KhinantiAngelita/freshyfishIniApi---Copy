<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles'; // Jika nama tabel berbeda, misalnya 'roles'
    protected $primaryKey = 'ID_role'; // Nama kolom primary key yang benar

    protected $fillable = ['role_name'];
}
