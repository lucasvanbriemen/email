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
        'user_id',
        'host',
        'port',
        'protocol',
        'encryption',
        'validate_cert',
        'username',
        'password',
    ];

    // On create
    protected static function booted()
    {
        // If an credential is made, we also want to create a folder for it
        static::created(function ($credential) {
            // Create default folders for the user
            foreach (Folder::$defaultFolders as $key => $name) {
                Folder::create([
                    'imap_credential_id' => $credential->id,
                    'name' => $name,
                    'path' => $key,
                ]);
            }
        });
    }
}
