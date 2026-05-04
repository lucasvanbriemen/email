<?php

namespace App;

class MailboxConfig
{
    public const GROUPS = [
        [
            'path' => 'home',
            'name' => 'Home',
            'ios_icon' => 'house',
            'rules' => [
                'exclude_from' => ['work', 'github', 'pathe']
            ],
        ],
        [
            'path' => 'work',
            'name' => 'Work',
            'ios_icon' => 'suitcase',
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
            'ios_icon' => 'chevron.left.forwardslash.chevron.right',
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
            'ios_icon' => 'popcorn',
            'rules' => [
                'from' => [
                    '*@service.pathe.nl'
                ],
            ],
        ],
    ];
}
