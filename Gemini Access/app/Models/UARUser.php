<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UARUser extends Model
{
    use HasFactory;
    protected $table = 'uar_users';

    protected $fillable = [
        'uar_id', 
        'user_data', 
        'primary_reviewer_id',
        'secondary_reviewer_id',
        'primary_review_status', 
        'secondary_review_status', 
        'primary_reviewed_at', 
        'secondary_reviewed_at'
    ];

    protected $casts = [
        'user_data' => 'array', // Automatically cast JSON to array
        'reviewed_at' => 'datetime',
        'primary_reviewer_id' => 'integer',
        'secondary_reviewer_id' => 'integer',

    ];

   
}
