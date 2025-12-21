<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Email;
use Illuminate\Support\Facades\DB;
use App\Models\Profile;

class DashboardController extends Controller
{
    public function index()
    {
        $profiles = Profile::where('user_id', currentUser()->id)->get();

        return redirect()->route('mailbox.overview', [
            'linked_profile_id' => $profiles->first()->linked_profile_count,
            'folder' => 'inbox'
        ]);
    }
}
