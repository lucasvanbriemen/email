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
        var_dump($last_activity);

        // Get all emails for the authenticated user after the last activity
        $emails = Email::whereIn('credential_id', $credentials->pluck('id'))
            ->where('created_at', '>', $last_activity)
            ->orderBy('created_at', 'desc')
            ->get();

        var_dump($emails);

        return view('dashboard');
    }

}
