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
        'sent_at',
        'has_read',
        'uid',
        'html_body'
    ];
}
