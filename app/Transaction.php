<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [
        'id'
    ];

    /**
     * Tipper user details
     */
    public function tipper()
    {
        return $this->hasMany('App\User', 'id', 'tipper_id');
    }

    /**
     * Tippe user details
     */
    public function tippe()
    {
        return $this->hasMany('App\User', 'id', 'tippe_id');
    }

    /**
     * tipper has one tippe
     */
    public function oneTippe()
    {
        return $this->hasOne('App\User', 'id', 'tippe_id');
    }

    /**
     * tippe has one tipper
     */
    public function oneTipper()
    {
        return $this->hasOne('App\User', 'id', 'tipper_id');
    }
}
