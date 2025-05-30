<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Webklex\IMAP\Facades\Client;

class AccountLayout extends Component
{
    public function render(): View
    {
        return view('layouts.account');
    }
}
