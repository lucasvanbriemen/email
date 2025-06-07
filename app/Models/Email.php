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
        'user_id',
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

    public static function getEmails($folder)
    {
        $query = Email::where('folder_id', $folder->id)
            ->where('user_id', auth()->id())
            ->where('is_archived', false);

        if (!str_contains(strtolower($folder->name), 'trash')) {
            $query->where('is_deleted', false);
        }

        return $query->orderBy('sent_at', 'desc')->get();
    }

    public static function deleteEmail($uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('user_id', auth()->id())
            ->first();

        if (!$email) {
            return;
        }
        $email->is_deleted = true;

        // Remove to trash
        $trashFolder = Folder::where('name', 'LIKE', '%trash%')
            ->where('user_id', auth()->id())
            ->first();

        if ($trashFolder) {
            $email->folder_id = $trashFolder->id;
        }

        $email->save();
    }
}
