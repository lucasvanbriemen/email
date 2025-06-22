<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpCredentials extends Model
{
    //
    protected $fillable = [
        'imap_credential_id',
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
