<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    //
    protected $fillable = [
        'email_id',
        'name',
        'path',
        'mime_type',
    ];
}
