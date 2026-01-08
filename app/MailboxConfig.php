<?php

namespace App;

class MailboxConfig
{
    public const GROUPS = [
        [
            'path' => 'home',
            'name' => 'Home',
            'rules' => [
                'exclude_from' => ['work', 'github', 'pathe']
            ],
        ],
        [
            'path' => 'work',
            'name' => 'Work',
            'rules' => [
                'from' => [
                    '*@webinargeek.com'
                ],
                'to' => [
                    '*@webinargeek.com'
                ]
            ],
        ],
        [
            'path' => 'github',
            'name' => 'GitHub',
            'rules' => [
                'from' => [
                    '*@github.com',
                    '*@notifications.github.com',
                ],
                'sender_name' => [
                    'github GUI'
                ],
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
