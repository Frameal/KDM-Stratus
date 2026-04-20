<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pc extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'pc_number',
        'status',
    ];

    // The reverse relationship
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}