<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingEmailSender extends Model
{
    protected $table = 'sender_email';

    protected $fillable = [
        'email',
        'name',
        'image_path',
        'top_level_domain',
    ];

    public function email_to_domain()
    {
        $email = $this->email;
        $full_domain = substr(strrchr($email, "@"), 1);
        
        $domain_parts = explode('.', $full_domain);
        $num_parts = count($domain_parts);

        $domain = $domain_parts[$num_parts - 2] . '.' . $domain_parts[$num_parts - 1];
        return $domain;
    }
}
