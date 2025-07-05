<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
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
                    'icon' => Folder::$defaultFolderIcons[$key] ?? null,
                    'path' => $key,
                ]);
            }

            foreach (Tag::$defaultTags as $tag) {
                Tag::create([
                    'name' => $tag['name'],
                    'color' => $tag['color'],
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
        $profile = Profile::where('user_id', $userid)
            ->where('id', $linkedProfileId)
            ->first();

        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $userid;

            $profile->name = auth()->user()->name; // Default to the user's name
            $profile->email = auth()->user()->email; // Default to the user's email
            $profile->linked_profile_count = 0; // Initialize linked profile count
        }

        return $profile;
    }
}
