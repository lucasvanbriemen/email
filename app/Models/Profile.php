<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;
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
                    'profile_id' => $credential->id,
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
        $userid = currentUser()->id;
        $profile = Profile::where('user_id', $userid)
            ->where('id', $linkedProfileId)
            ->first();

        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $userid;

            $profile->name = currentUser()->name; // Default to the user's name
            $profile->email = currentUser()->email; // Default to the user's email
            $profile->linked_profile_count = 0; // Initialize linked profile count
        }

        return $profile;
    }
}
