<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    //
      /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    public static $defaultFolders = [
        'inbox' => 'Inbox',
        'sent' => 'Sent',
        'drafts' => 'Drafts',
        'trash' => 'Trash',
        'spam' => 'Spam',
        'all' => 'All Emails',
    ];

    protected $fillable = [
        'imap_credential_id',
        'name',
        'path',
    ];
}
