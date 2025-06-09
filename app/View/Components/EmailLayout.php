<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmailLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */

    protected $client;
    protected $DEFAULT_FOLDER = 'inbox';
    protected $selectedFolder;
    protected $imapCredentials = [];
    protected $selectedCredential;

    public function __construct($selectedCredential = null, $selectedFolder = null)
    {
        if ($selectedFolder) {
            $this->selectedFolder = $selectedFolder;
        } else {
            $this->selectedFolder = $this->DEFAULT_FOLDER;
        }

        $this->imapCredentials = DB::table('imap_credentials')
            ->where('user_id', auth()->id())
            ->get();

        if ($selectedCredential) {
            $this->selectedCredential = $selectedCredential;
        } else {
            $this->selectedCredential = $this->imapCredentials[0] ?? null;
        }
    }

    public function render(): View
    {
        $folders = Folder::where('imap_credential_id', User::find(auth()->id())->imapCredential->id)->get();

        return view('layouts.email',
            [
                'folders' => $folders,
                'selectedFolder' => $this->selectedFolder,
                'imapCredentials' => $this->imapCredentials,
                'selectedCredential' => $this->selectedCredential
            ]
        );
    }
}
