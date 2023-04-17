<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
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
     * Custom tabel name for feedback
     *
     * @var string
     */
    protected $table = 'feedbacks';

    /**
     * relation with user.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
