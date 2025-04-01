<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UARFile extends Model
{
    use HasFactory;

    protected $table = 'uar_files'; // Ensure correct table name
    protected $fillable = ['uar_id', 'user_list', 'screenshot'];
}
