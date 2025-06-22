<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AccountLayout extends Component
{

    protected $profiles = [];
    protected $selectedProfile = null;

    public function __construct($profiles = null, $selectedProfile = null)
    {
        if ($profiles) {
            $this->profiles = $profiles;
        } else {
            $this->profiles = collect();
        }

        if ($selectedProfile) {
            $this->selectedProfile = $selectedProfile;
        } else {
            $this->selectedProfile = $this->profiles[0];
        }

    }

    public function render(): View
    {
        return view('layouts.account',
            [
                'profiles' => $this->profiles,
                'selectedProfile' => $this->selectedProfile,
            ]
        );
    }
}
