<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImapCredentials;
use App\Models\Email;
use Cloudstudio\Ollama\Facades\Ollama;

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

        $ollama_system_prompt = "You are an AI assistant that helps users summarize their emails. " .
            "You will be given a list of emails and you need to provide a concise summary of the most important information contained within them Try to be as concise as possible, but also provide enough information to understand the context of the emails. 100 words max.";
        $ollama_prompt = "Here is the list of emails:\n\n";

        foreach ($credentials as $credential) {
            foreach ($emails as $email) {

                if ($email->credential_id !== $credential->id) {
                    continue; // Skip emails that do not belong to the current credential
                }

                $ollama_prompt .= "Email account: {$credential->username}\n" .
                    "Email subject: {$email->subject}\n" .
                    "Email body: {$email->body}\n\n";
            }
        }


        $response = Ollama::agent($ollama_system_prompt)
            ->prompt($ollama_prompt)
            ->options(['temperature' => 0.8])
            ->stream(false)
            ->ask();

        return view(
            'dashboard',
            [
                'credentials' => $credentials,
                'emails' => $emails,
                'last_activity' => $last_activity,
                'ollama_response' => $response,
            ]
        );
    }

}
