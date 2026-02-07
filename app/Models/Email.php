<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Folder;
use Illuminate\Support\Str;

class Email extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'profile_id',
        'sender_id',
        'sender_name',
        'subject',
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

    protected $casts = [
        'sent_at' => 'datetime',
        'has_read' => 'boolean',
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

    public function sender()
    {
        return $this->belongsTo(IncomingEmailSender::class, 'sender_id', 'id');
    }

    public function getSenderDisplayName()
    {
        return $this->sender_name;
    }

    public static function getEmails($folder, $profile, $offset = 0)
    {
        if (!$folder) {
            return collect();
        }

        $query = Email::where('profile_id', $profile->id);

        // If the folder is NOT a custom view folder, we filter by folder ID
        if (!in_array($folder->path, Email::$customViewFolders)) {
            $query->where('folder_id', $folder->id);
            $query->where('is_archived', false);
        }

        if ($folder->path == 'trash') {
            $query->where('is_deleted', true);
        }

        if ($folder->path == 'spam') {
            $query->where('is_deleted', false)
                ->where("profile_id", "-1");
        }

        if ($folder->path == 'stared') {
            $query->where('is_starred', true);
        }

        return $query->orderBy('sent_at', 'desc')->offset($offset)->limit(50)->get();
    }

    public static function deleteEmail($uuid, $profile_id)
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
            ->where('profile_id', $profile_id)
            ->first();

        $email->folder_id = $trashFolder->id;

        $email->save();
    }

    public function getPreview()
    {
        $body = $this->html_body;

        // Extract text content from HTML and clean it up
        $body = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $body);
        $body = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $body);
        $body = preg_replace('/<!--.*?-->/is', '', $body);
        $body = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $body);
        $body = preg_replace('/<\!DOCTYPE[^>]*>/is', '', $body);
        $body = strip_tags($body);
        $body = html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $body = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $body);
        $body = preg_replace('/\s+/u', ' ', $body);
        $body = preg_replace('/\.{2,}/', '.', $body);
        $body = trim($body);

        if (empty($body)) {
            return '';
        }

        $preview = mb_substr($body, 0, 100);

        if (mb_strlen($body) > 100) {
            $preview .= '...';
        }

        return $preview;
    }
}
