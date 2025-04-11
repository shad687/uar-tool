<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'organization_code'];

    public function users()
    {
        return $this->hasMany(User::class, 'organization_code', 'organization_code');
    }
}
