<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmtpCredentials extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'profile_id',
        'host',
        'port',
        'username',
        'password',
        'reply_to_name',
        'reply_to_email',
        'from_name',
        'from_email',
    ];
}
