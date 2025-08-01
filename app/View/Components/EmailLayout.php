<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Models\Folder;
use App\Models\Profile;
use Illuminate\Support\Facades\Route;

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
    protected $class = '';

    public function __construct($class = '')
    {
        $selectedProfile = Route::current()->parameter('linked_profile_id');
        $this->selectedProfile = Profile::where('user_id', currentUser()->id)
            ->where('linked_profile_count', $selectedProfile)
            ->first();


        $selectedFolder = Route::current()->parameter('folder') ?? $this->DEFAULT_FOLDER;
        $this->selectedFolder = Folder::where('profile_id', $this->selectedProfile->id)
            ->where('path', $selectedFolder)
            ->first();

        $this->class = $class;

        $this->profiles = Profile::where('user_id', currentUser()->id)->get();

    }

    public function render(): View
    {
        $folders = Folder::where('profile_id', $this->selectedProfile->id)->get();

        return view(
            'layouts.email',
            [
                'class' => $this->class,
                'folders' => $folders,
                'selectedFolder' => $this->selectedFolder,
                'profiles' => $this->profiles,
                'selectedProfile' => $this->selectedProfile
            ]
        );
    }
}
