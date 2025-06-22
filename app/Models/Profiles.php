<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profiles extends Model
{
    //
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'linked_profile_count'
    ];
}
