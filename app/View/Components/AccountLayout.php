<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Webklex\IMAP\Facades\Client;

class AccountLayout extends Component
{

    protected $profiles = [];

    public function __construct($profiles = null)
    {
        if ($profiles) {
            $this->profiles = $profiles;
        } else {
            $this->profiles = collect();
        }
    }

    public function render(): View
    {
        return view('layouts.account',
            [
                'profiles' => $this->profiles,
            ]
        );
    }
}
