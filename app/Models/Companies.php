<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'description','isActive'];
    protected $casts = [
        'is_admin' => 'boolean',
    ];
}
