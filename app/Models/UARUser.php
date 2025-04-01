<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UARUser extends Model
{
    use HasFactory;
    protected $table = 'uar_users';

    protected $fillable = ['uar_id', 'user_data', 'status', 'reviewer_id', 'reviewed_at'];

    protected $casts = [
        'user_data' => 'array', // Automatically cast JSON to array
        'reviewed_at' => 'datetime',
    ];

   
}
