<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use  App\Models\Folder;

class ImapCredentials extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'profile_id',
        'host',
        'port',
        'protocol',
        'encryption',
        'username',
        'password',
    ];

    protected static function booted()
    {
        // If an imap is made, we want to set the validate_cert to true by default
        static::created(function ($imapCedential) {
            // Set validate_cert to true by default
            $imapCedential->validate_cert = true;
            $imapCedential->save();
        });
    }

}
