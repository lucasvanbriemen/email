<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImapCredentials;
use App\Models\Email;
use App\Models\Profile;

class DashboardController extends Controller
{
    //

    public function index()
    {

        $profiles = Profile::where('user_id', auth()->id())->get();

        $last_activity = auth()->user()->last_activity;

        $emails = Email::whereIn('profile_id', $profiles->pluck('id'))
            ->where('created_at', '>', $last_activity)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($emails as $email) {
            // set the profile_id to the linked_profile_count
            $email->profile_id = Profile::where('id', $email->profile_id)->first()->linked_profile_count;
        }

        $ai_summery = [
            'status' => 'error',
            'response' => 'We encountered an issue while processing your emails. Please try again later.',
            'data' => []
        ];

        return view(
            'dashboard',
            [
            'profiles' => $profiles,
            'emails' => $emails,
            'last_activity' => $last_activity,
            'ollama_response' => $ai_summery,
            ]
        );
    }
}
