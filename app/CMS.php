<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CMS extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * Custom tabel name for CMS
     *
     * @var string
     */
    protected $table = 'cms';
}
