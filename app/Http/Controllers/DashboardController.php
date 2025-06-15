<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImapCredentials;
use App\Models\Email;

class DashboardController extends Controller
{
    //

    public function index()
    {

        // Get all IMAP credentials for the authenticated user
        $credentials = ImapCredentials::where('user_id', auth()->id())->get();

        // get the last time the user logged in
        $last_activity = auth()->user()->last_activity;

        // Get all emails for the authenticated user after the last activity
        $emails = Email::whereIn('credential_id', $credentials->pluck('id'))
            ->where('created_at', '>', $last_activity)
            ->orderBy('created_at', 'desc')
            ->get();

        $ollama_system_prompt = "You are an AI assistant analyzing emails from a user's inbox since their last activity. Extract only relevant key events, tasks, decisions, deadlines, or important updates. Exclude emails without meaningful content. Provide a clean, bullet-pointed list with no greetings, signatures, filler, or markup. Deliver strictly factual, concise points only.";
        $ollama_prompt = "Here is the list of emails:\n\n";
        foreach ($emails as $email) {
            $ollama_prompt .= "Email subject: {$email->subject}\n" .
                "Email body: {$email->body}\n\n";
        }


        $ai_summery = ollama($ollama_system_prompt, $ollama_prompt);
        
        return view(
            'dashboard',
            [
                'credentials' => $credentials,
                'emails' => $emails,
                'last_activity' => $last_activity,
                'ollama_response' => $ai_summery,
            ]
        );
    }

}
