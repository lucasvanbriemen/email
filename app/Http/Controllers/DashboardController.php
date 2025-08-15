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

        // If there is only one, we dont need to show the profile selection
        if ($profiles->count() === 1) {
            // return redirect()->route('mailbox.overview', ['linked_profile_id' => $profiles->first()->id, 'folder' => 'inbox']);
        }

        $last_activity = currentUser()->last_activity;

        $emails = Email::whereIn('profile_id', $profiles->pluck('id'))
            ->where('created_at', '>', $last_activity)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($emails as $email) {
            // set the profile_id to the linked_profile_count
            $email->profile_id = Profile::where('id', $email->profile_id)->first()->linked_profile_count;
        }

        $last_update = DB::table('system_info')->value('last_email_fetched_at');

        return view(
            'dashboard',
            [
                'profiles' => $profiles,
                'emails' => $emails,
                'last_activity' => $last_activity,
                'last_update' => $last_update
            ]
        );
    }
}
