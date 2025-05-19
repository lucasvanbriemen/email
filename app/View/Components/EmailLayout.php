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
    protected $DEFAULT_FOLDER = 'INBOX';
    protected $selectedFolder;

    public function __construct($selectedFolder = null)
    {
        $this->client = Client::account('default');
        $this->client->connect();

        if ($selectedFolder) {
            $this->selectedFolder = $this->client->getFolder($selectedFolder);
        } else {
            $this->selectedFolder = $this->client->getFolder($this->DEFAULT_FOLDER);
        }
    }

    public function render(): View
    {
        return view('layouts.email',
            [
                'folders' => $this->client->getFolders(false),
                'selectedFolder' => $this->selectedFolder,
            ]
        );
    }
}
