<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Models\Folder;

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

        if ($selectedFolder) {
            $this->selectedFolder = $selectedFolder;
        } else {
            $this->selectedFolder = $this->DEFAULT_FOLDER;
        }
    }

    public function render(): View
    {
        $folders = Folder::where('user_id', auth()->id())->get();

        return view('layouts.email',
            [
                'folders' => $folders,
                'selectedFolder' => $this->selectedFolder,
            ]
        );
    }
}
