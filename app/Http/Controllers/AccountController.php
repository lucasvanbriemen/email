<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ImapCredentials;
use App\Models\SmtpCredentials;

class AccountController extends Controller
{
    //
    public function edit()
    {

        // get the current user's IMAP credentials
        $imapCredentials = ImapCredentials::where('user_id', auth()->id())->get();

        return view('account.credentials', [
            'imap_credentials' => $imapCredentials,
        ]);
    }

    public function storeImapCredentials()
    {
        // Validate and store IMAP credentials
        request()->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);


        ImapCredentials::create(
            [
                'user_id' => auth()->id(),
                'host' => request('host'),
                'port' => request('port'),
                'username' => request('username'),
                'password' => request('password'),
            ]
        );

        return redirect()->route('account.edit')->with('status', 'IMAP credentials updated successfully.');
    }

    public function storeSmtpCredentials()
    {
        SmtpCredentials::create(
            [
                'imap_credential_id' => request('imap_credential_id'),
                'host' => request('host'),
                'port' => request('port'),
                'username' => request('username'),
                'password' => request('password'),
                'reply_to_name' => request('reply_to_name'),
                'reply_to_email' => request('reply_to_email'),
                'from_name' => request('from_name'),
                'from_email' => request('from_email')
            ]
        );

        return redirect()->route('account.edit')->with('status', 'IMAP credentials updated successfully.');
    }
}
