<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // This allows us to safely update the pricing values from the HQ dashboard
    protected $fillable = [
        'key',
        'value',
    ];
}