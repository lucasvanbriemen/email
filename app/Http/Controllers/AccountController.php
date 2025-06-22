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

    public function storeSmtpCredentials($linked_profile_id = null)
    {
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        request()->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'reply_to_name' => 'nullable|string|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'from_name' => 'nullable|string|max:255',
            'from_email' => 'nullable|email|max:255',
        ]);

        $smtpCredential = SmtpCredentials::where('profile_id', $profile->id)->first();
        if (!$smtpCredential) {
            $smtpCredential = new SmtpCredentials();
        }

        $smtpCredential->profile_id = $profile->id;
        $smtpCredential->host = request('host');
        $smtpCredential->port = request('port');
        $smtpCredential->username = request('username');
        $smtpCredential->password = request('password');
        $smtpCredential->reply_to_name = request('reply_to_name');
        $smtpCredential->reply_to_email = request('reply_to_email');
        $smtpCredential->from_name = request('from_name');
        $smtpCredential->from_email = request('from_email');

        $smtpCredential->save();

        return redirect('/account/' . $linked_profile_id)->with('status', 'SMTP credentials updated successfully.');
    }
}
