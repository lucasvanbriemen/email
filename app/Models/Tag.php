<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;
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
            // Only set profile_id if not already set
            if (!$tag->profile_id) {
                $user = currentUser();
                $tag->profile_id = $user->profile_id ?? $user->id ?? 1;
            }
        });
    }

    protected $fillable = [
        'profile_id',
        'name',
        'color',
    ];
}
