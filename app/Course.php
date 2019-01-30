<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{

    protected $fillable = [
        'title',
        'author',
        'description',
        'image',
        'price',
        'price_text'
    ];

    protected $hidden = [];

    public function lessons()
    {
        return $this->hasMany('App\Lesson')->orderBy('ordem', 'DESC');
    }
}
