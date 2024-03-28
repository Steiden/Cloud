<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $fillable = ['name', 'original_name', 'uri', 'current_dir', 'size', 'file_type_id'];
}
