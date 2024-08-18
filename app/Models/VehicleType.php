<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class VehicleType extends Model
{
    public $table = 'vehicleTypes';

    protected $fillable = [
        'cost_km',
        'minimum',
        'number',
    ];

}
