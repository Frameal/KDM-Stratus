<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'reference_id',
        'amount',
        'status',
    ];

    // This creates a relationship back to your User table
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}