<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'is_starred'
    ];

    public static function getEmails($folder){
        return Email::where('folder_id', $folder->id)
            ->where('user_id', auth()->id())
            ->where('is_archived', false)
            ->orderBy('sent_at', 'desc')
            ->get();
    }
}
