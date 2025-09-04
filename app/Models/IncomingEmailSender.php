<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected static function booted() {
        static::created(function ($sender) {
            // Automatically fetch and store the logo when a new sender is created
            $sender->store_domain_as_logo();
        });
    }


    public static function email_to_domain($email)
    {
        $full_domain = substr(strrchr($email, "@"), 1);

        if (!$full_domain) {
            return '';
        }

        $domain_parts = explode('.', $full_domain);
        $num_parts = count($domain_parts);

        if ($num_parts < 2) {
            return $full_domain;
        }

        $suffixTwoParts = $domain_parts[$num_parts - 2] . '.' . $domain_parts[$num_parts - 1];

        // Handle common multi-label public suffixes (e.g., co.in), defaulting to eTLD+1
        $multiLabelSuffixes = [
            'co.in', 'net.in', 'org.in', 'edu.in', 'gov.in',
            'co.uk', 'org.uk', 'ac.uk', 'gov.uk',
            'com.au', 'net.au', 'org.au',
            'co.jp',
            'com.br', 'com.mx',
        ];

        if (in_array($suffixTwoParts, $multiLabelSuffixes, true) && $num_parts >= 3) {
            return $domain_parts[$num_parts - 3] . '.' . $suffixTwoParts;
        }

        return $suffixTwoParts;
    }

    public function store_domain_as_logo()
    {
        $domain = $this->top_level_domain ?? self::email_to_domain($this->email);

        $logo_url = "https://img.logo.dev/{$domain}?token=pk_YHpEPFuOTnGDZ6nmBhgIog&retina=true";

        // Store this image in $this->filePath with a unique name
        $image_content = @file_get_contents($logo_url);
        if ($image_content) {
            $image_name = uniqid('logo_') . '.png';
            $full_path = public_path($this->filePath . $image_name);

            $dir = dirname($full_path);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            file_put_contents($full_path, $image_content);
            $this->image_path = $this->filePath . $image_name;
            $this->save();
        }
    }

    /**
     * Accessor for the computed logo URL.
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_path ?: null,
        );
    }
}
