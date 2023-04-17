<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'data' => 'array'
    ];
}
