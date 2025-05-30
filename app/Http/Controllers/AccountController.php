<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ImapCredentials;

class AccountController extends Controller
{
    //
    public function edit()
    {

        // get the current user's IMAP credentials
        $imapCredentials = ImapCredentials::where('user_id', auth()->id())->first();

        return view('account.credentials', [
            'imap_credentials' => $imapCredentials,
        ]);
    }
}
