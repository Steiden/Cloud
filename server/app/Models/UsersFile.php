<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersFile extends Model
{
    use HasFactory;

    protected $guarded = false;
    protected $fillable = ['user_id', 'file_id', 'access_type_id'];
}
