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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function imapCredentials()
    {
        return $this->hasMany(ImapCredentials::class);
    }

    public static function linkedProfileIdToProfile($linkedProfileId)
    {
        $userid = auth()->user()->id;
        $profile = Profiles::where('user_id', $userid)
            ->where('id', $linkedProfileId)
            ->first();

        return $profile;
    }
}
