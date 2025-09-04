<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
    use HasFactory;
    //
      /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    public static $defaultFolders = [
        'inbox' => 'Inbox',
        'stared' => 'Stared',
        'sent' => 'Sent',
        'drafts' => 'Drafts',
        'trash' => 'Trash',
        'spam' => 'Spam',
        'all' => 'All Emails',
    ];

    public static $defaultFolderIcons = [
        'inbox' => 'email',
        'stared' => 'star',
        'sent' => 'send',
        'drafts' => 'draft',
        'trash' => 'trash',
        'spam' => 'exclamation',
        'all' => 'emails',
    ];

    protected $fillable = [
        'profile_id',
        'name',
        'icon',
        'path',
    ];
}
