<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketSegment extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'description','active'];
    protected $casts = [
        'active' => 'boolean',
    ];
}
