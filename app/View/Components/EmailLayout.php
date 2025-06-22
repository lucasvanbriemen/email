<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Models\Folder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

class EmailLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */

    protected $client;
    protected $DEFAULT_FOLDER = 'inbox';
    protected $selectedFolder;
    protected $profiles = [];
    protected $selectedProfile;

    public function __construct($selectedProfile = null, $selectedFolder = null)
    {
        if ($selectedFolder) {
            $this->selectedFolder = $selectedFolder;
        } else {
            $this->selectedFolder = $this->DEFAULT_FOLDER;
        }

        $this->profiles = Profile::where('user_id', auth()->id())->get();

        if ($selectedProfile) {
            $this->selectedProfile = $selectedProfile;
        } else {
            $this->selectedProfile = $this->profiles[0] ?? null;
        }
    }

    public function render(): View
    {
        $folders = Folder::where('profile_id', $this->selectedProfile->id)->get();

        return view('layouts.email',
            [
                'folders' => $folders,
                'selectedFolder' => $this->selectedFolder,
                'profiles' => $this->profiles,
                'selectedProfile' => $this->selectedProfile
            ]
        );
    }
}
