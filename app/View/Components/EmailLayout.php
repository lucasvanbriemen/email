<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Models\Folder;
use App\Models\User;

class EmailLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */

    protected $client;
    protected $DEFAULT_FOLDER = 'inbox';
    protected $selectedFolder;

    public function __construct($selectedFolder = null)
    {

        if ($selectedFolder) {
            $this->selectedFolder = $selectedFolder;
        } else {
            $this->selectedFolder = $this->DEFAULT_FOLDER;
        }
    }

    public function render(): View
    {
        $folders = Folder::where('imap_credential_id', User::find(auth()->id())->imapCredential->id)->get();

        return view('layouts.email',
            [
                'folders' => $folders,
                'selectedFolder' => $this->selectedFolder,
            ]
        );
    }
}
