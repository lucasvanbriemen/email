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

    /**
     * values that should be set by the model.
     *
     * @var array
     */
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

    public static function deleteEmail($folder, $uid)
    {
        // convert the folder name into an folder id
        $folder = Folder::where('name', $folder)
            ->where('user_id', auth()->id())
            ->first();

        $email = Email::where('uid', $uid)
            ->where('user_id', auth()->id())
            ->where('folder_id', $folder->id)
            ->first();

        if (!$email) {
            return;
        }

        $email->is_deleted = true;
        $email->save();

        // Optionally, you can also move the email to a "Trash" folder
        $trashFolder = Folder::where('name', 'LIKE', '%trash%')
            ->where('user_id', auth()->id())
            ->first();

        if ($trashFolder) {
            $email->folder_id = $trashFolder->id;
            $email->save();
        }
    }
}
