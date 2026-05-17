<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'total_pcs',
    ];

    // THIS IS THE MISSING PIECE
    public function pcs()
    {
        return $this->hasMany(Pc::class);
    }
}