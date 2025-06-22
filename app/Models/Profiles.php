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
