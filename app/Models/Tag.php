<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //

    public static $defaultTags = [
        ['name' => 'Work', 'color' => '#FF5733'],
        ['name' => 'Personal', 'color' => '#33FF57'],
        ['name' => 'Important', 'color' => '#3357FF'],
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            $tag->profile_id = currentUser()->profile_id;
        });
    }

    protected $fillable = [
        'profile_id',
        'name',
        'color',
    ];
}
