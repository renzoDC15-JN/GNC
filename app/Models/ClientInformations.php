<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientInformations extends Model
{
    use HasFactory;
    protected $fillable = [
        'project',
        'location',
        'property_name',
        'phase',
        'block',
        'lot',
        'buyer_name',
        'buyer_civil_status',
        'buyer_nationality',
        'buyer_address',
        'buyer_tin',
        'buyer_spouse_name',
        'mrif_fee',
        'reservation_rate',
        'created_by',
    ];
}
