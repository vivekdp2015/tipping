<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationSound extends Model
{
    protected $guarded = [
        'id'
    ];

    public function type()
    {
        return $this->belongsTo('App\NotificationType');
    }

    public function getSoundAttribute($sound)
    {
        return env("APP_URL", 'http://tipping-jar.preview.cx').'/uploads/sounds/'.$sound;
    }
}
