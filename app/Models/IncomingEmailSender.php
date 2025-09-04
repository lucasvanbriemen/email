<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingEmailSender extends Model
{

    protected $table = 'sender_email';
    protected $filePath = 'attachments/logos/';

    protected $fillable = [
        'email',
        'name',
        'image_path',
        'top_level_domain',
    ];

    public function emails()
    {
        return $this->belongsToMany(Email::class, 'sender_email');
    }

    public function email_to_domain()
    {
        $email = $this->email;
        $full_domain = substr(strrchr($email, "@"), 1);
        
        $domain_parts = explode('.', $full_domain);
        $num_parts = count($domain_parts);

        return $domain_parts[$num_parts - 2] . '.' . $domain_parts[$num_parts - 1];
    }

    public function store_domain_as_logo()
    {
        $domain = $this->top_level_domain ?? $this->email_to_domain();

        $logo_url = "https://img.logo.dev/{$domain}?token=pk_YHpEPFuOTnGDZ6nmBhgIog&retina=true";

        // Store this image in $this->filePath with a unique name
        $image_content = file_get_contents($logo_url);
        if ($image_content) {
            $image_name = uniqid('logo_') . '.png';
            $full_path = public_path($this->filePath . $image_name);
            file_put_contents($full_path, $image_content);
            $this->image_path = $this->filePath . $image_name;
            $this->save();
        }
    }

    public function logo_url()
    {
        return $this->image_path ?: null;
    }
}
