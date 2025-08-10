<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    //
    protected $fillable = [
        'email_id',
        'name',
        'path',
        'mime_type',
    ];


    public function email()
    {
        return $this->belongsTo(Email::class);
    }

    public function getContent()
    {
        return file_get_contents($this->path);
    }
}
