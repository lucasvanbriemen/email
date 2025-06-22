<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImapCredentials;
use App\Models\Email;
use App\Models\Profiles;

class DashboardController extends Controller
{
    //

    public function index()
    {

        $profiles = Profiles::where('user_id', auth()->id())->get();

        $last_activity = auth()->user()->last_activity;

        $emails = Email::whereIn('profile_id', $profiles->pluck('id'))
            ->where('created_at', '>', $last_activity)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($emails as $email) {
            // set the profile_id to the linked_profile_count
            $email->profile_id = Profiles::where('id', $email->profile_id)->first()->linked_profile_count;
        }

        $ollama_system_prompt = "You are an AI assistant analyzing emails from a user's inbox since their last activity. Extract only relevant key events, tasks, decisions, deadlines, or important updates. Exclude emails without meaningful content. Provide a clean, bullet-pointed list with no greetings, signatures, filler, or markup. Deliver strictly factual, concise points only.";
        $ollama_prompt = "Here is the list of emails:\n\n";
        foreach ($emails as $email) {
            $ollama_prompt .= "Email subject: {$email->subject}\n" .
            "Email body: {$email->body}\n\n";
        }


        if (count($emails) > 0) {
            $ai_summery = ollama($ollama_system_prompt, $ollama_prompt);
        } else {
            $ai_summery = [
            'status' => 'success',
            'response' => 'No new emails since your last activity.',
            'data' => []
            ];
        }

        // if it contains 'error' as a key in the response, it means there was an error
        if (isset($ai_summery['error'])) {
            $ai_summery = [
                'status' => 'error',
                'response' => 'We encountered an issue while processing your emails. Please try again later.',
                'data' => []
            ];
        }

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
