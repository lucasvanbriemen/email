<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ImapCredentials;
use App\Models\SmtpCredentials;
use App\Models\Profile;

class AccountController extends Controller
{
    //
    public function edit($linked_profile_id = null)
    {
        // get the current user's IMAP credentials
        $profiles = Profile::where('user_id', auth()->id())->get();


        if ($linked_profile_id) {
            $seltectedProfile = Profile::linkedProfileIdToProfile($linked_profile_id);
        } else {
            $seltectedProfile = Profile::linkedProfileIdToProfile($profiles->first()->id);
        }

        $imapCredentials = ImapCredentials::where('profile_id', $seltectedProfile->id)->first();
        if (!$imapCredentials) {
            $imapCredentials = new ImapCredentials();
        }

        $smtpCredentials = SmtpCredentials::where('profile_id', $seltectedProfile->id)->first();
        if (!$smtpCredentials) {
            $smtpCredentials = new SmtpCredentials();
        }

        return view('account.credentials', [
            'profiles' => $profiles,
            'selectedProfile' => $seltectedProfile,
            'imapCredentials' => $imapCredentials,
            'smtpCredentials' => $smtpCredentials,
        ]);
    }

    public function storeImapCredentials($linked_profile_id = null)
    {

        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        // Validate and store IMAP credentials
        request()->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        // Check if we have an existing IMAP credential for this profile
        // If not, create a new one
        // If we have an existing one, update it
        $imapCredential = ImapCredentials::where('profile_id', $profile->id)->first();
        if (!$imapCredential) {
            $imapCredential = new ImapCredentials();
        }

        $imapCredential->profile_id = $profile->id;
        $imapCredential->host = request('host');
        $imapCredential->port = request('port');
        $imapCredential->username = request('username');
        $imapCredential->password = request('password');
        $imapCredential->encryption = request('encryption', 'ssl');

        $imapCredential->save();

        return redirect('/account/' . $linked_profile_id)->with('status', 'IMAP credentials updated successfully.');
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
