<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * This is to get JWT identifier
     *
     * @param void
     * @return string
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * This is JWT custom claims
     *
     * @param void
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * user has many categories
     */
    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }

    /**
     * Thiw will check user type
     */
    public function checkAdmin($user_types)
    {
        return (in_array(auth()->user()->type, $user_types)) ? true : false;
    }

    /**
     * This will add path to img url.
     */
    public function getProfileImgAttribute(string $img = null)
    {
        if (!empty($img) && (false === strpos($img, env("APP_URL", 'http://tipping-jar.preview.cx')))) {
            return env("APP_URL", 'http://tipping-jar.preview.cx').'/uploads/profile-images/'.$img;
        }

        return $img;
    }

    /**
     * one user has many tipps
     */
    public function tipps()
    {
        return $this->hasMany('App\Transaction', 'tippe_id');
    }

    /**
     * user has one notification type
     */
    public function notificationType()
    {
        return $this->hasOne('App\NotificationType', 'id', 'notification_type_id');
    }

    /**
     * User hasmany notifications
     */
    public function notifications()
    {
        return $this->hasMany('App\Notification');
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->device_token;
    }
}
