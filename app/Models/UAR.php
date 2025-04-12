<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UARFile;

class UAR extends Model
{
    use HasFactory;
    const STATUS_PENDING = 'pending';
    const STATUS_PRIMARY_REVIEW = 'primary_review';
    const STATUS_SECONDARY_REVIEW = 'secondary_review';
    const STATUS_REJECTED_USERS = 'rejected_users';
    const STATUS_COMPLETED = 'completed';



    protected $fillable = [
        'application',
        'app_owner_id',
        'primary_reviewer_id',
        'secondary_reviewer_id',
        'frequency',
        'start_at',
        'next_due',
        'organization_code',
        'created_by',
        'status',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_code', 'organization_code');
    }

    public function appOwner()
    {
        return $this->belongsTo(User::class, 'app_owner_id');
    }

    public function primaryReviewer()
    {
        return $this->belongsTo(User::class, 'primary_reviewer_id');
    }

    public function secondaryReviewer()
    {
        return $this->belongsTo(User::class, 'secondary_reviewer_id');
    }

    public function files()
    {
        return $this->hasMany(UARFile::class, 'uar_id');
    }

    public function users()
    {
        return $this->hasMany(UARUser::class, 'uar_id');
    }
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPrimaryReview()
    {
        return $this->status === self::STATUS_PRIMARY_REVIEW;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

}
