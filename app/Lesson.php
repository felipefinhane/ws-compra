<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'ordem',
        'titulo',
        'tempo',
        'video'
    ];

    protected $hidden = [];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }
}
