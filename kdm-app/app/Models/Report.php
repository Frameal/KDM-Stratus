<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_name',
        'concern_type',
        'details',
        'is_anonymous',
        'status' // FIXED: Added status so the database allows updates!
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}