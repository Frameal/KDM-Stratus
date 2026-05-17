<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pc_id',
        'branch_id',
        'fee_paid',
        'duration_minutes',
        'expires_at',
        'status'
    ];

    // Connects the reservation to the specific PC
    public function pc()
    {
        return $this->belongsTo(Pc::class);
    }

    // Connects the reservation to the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Connects the reservation to the branch
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}