<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ImapCredentials extends Authenticatable
{
    use HasFactory;
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
        'last_fetched_at',
        'last_fetch_error',
        'fetch_attempts',
    ];

    /**
     * Get the profile that owns this IMAP credential
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

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
