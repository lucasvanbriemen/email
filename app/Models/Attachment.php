<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;
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
