<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class City extends Model
{
    public $table = 'cities';

    protected $fillable = [
        'name',
        'zipCode',
        'country',
    ];

}
