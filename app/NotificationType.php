<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    protected $guarded = [
        'id'
    ];

    public function sound()
    {
        return $this->hasOne('App\NotificationSound', 'id', 'notification_sounds_id');
    }

    public function getImgAttribute($img)
    {
        return env("APP_URL", 'http://tipping-jar.preview.cx').'/uploads/notifications/'.$img;
    }
}
