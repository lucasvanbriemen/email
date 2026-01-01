<?php

namespace App;

class MailboxConfig
{
    public const GROUPS = [
        [
            'path' => 'home',
            'name' => 'Home',
        ],
        [
            'path' => 'work',
            'name' => 'Work',
            'rules' => [
                'from' => [
                    '*@webinargeek.com'
                ]
            ],
        ],
        [
            'path' => 'github',
            'name' => 'GitHub',
            'rules' => [
                'from' => [
                    '@github.com',
                    '@notifications.github.com',
                ]
            ],
        ],
        [
            'path' => 'pathe',
            'name' => 'Pathe',
            'rules' => [
                'from' => [
                    '*@service.pathe.nl'
                ],
            ],
        ],
    ];
}
