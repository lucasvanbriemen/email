<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Folder;
use Illuminate\Support\Str;

class Email extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'profile_id',
        'subject',
        'from',
        'sender_email',
        'to',
        'sent_at',
        'has_read',
        'uid',
        'html_body',
        'folder_id',
        'is_archived',
        'is_starred',
        'is_deleted',
    ];

    protected $garded = [
        'uuid',
    ];

    public static $customViewFolders = [
        'trash',
        'all',
        'spam',
        'stared',
    ];

    protected static function booted()
    {
        static::creating(function ($email) {
            $email->uuid = Str::uuid()->toString();
        });
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getEmails($folder, $profile)
    {
        if (!$folder) {
            return collect();
        }

        $query = Email::where('profile_id', $profile->id);

        // If the folder is NOT a custom view folder, we filter by folder ID
        if (!in_array($folder->path, Email::$customViewFolders)) {
            $query->where('folder_id', $folder->id);
            $query->where('is_archived', false);


            return $query->orderBy('sent_at', 'desc')->limit(50)->get();
        }

        if ($folder->path == 'trash') {
            $query->where('is_deleted', true);
        }

        if ($folder->path == 'spam') {
            $query->where('is_deleted', false)
                ->where("credential_id", "-1");
        }

        if ($folder->path == 'stared') {
            $query->where('is_starred', true);
        }

        return $query->orderBy('sent_at', 'desc')->get();
    }

    public static function deleteEmail($uuid, $credential_id)
    {
        $email = Email::where('uuid', $uuid)
            ->first();

        if (!$email) {
            return;
        }
        $email->is_deleted = true;
        $email->save();

        // Remove to trash
        $trashFolder = Folder::where('path', 'trash')
            ->where('imap_credential_id', $credential_id)
            ->first();

        $email->folder_id = $trashFolder->id;

        $email->save();
    }
}
