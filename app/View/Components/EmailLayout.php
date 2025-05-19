<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Webklex\IMAP\Facades\Client;

class EmailLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */

    protected $client;

    public function __construct()
    {
        $this->client = Client::account('default');
        $this->client->connect();
    }

    public function render(): View
    {
        return view('layouts.email',
            [
                'folders' => $this->client->getFolders(false),
            ]
        );
    }
}
