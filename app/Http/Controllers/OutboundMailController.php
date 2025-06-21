<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Transport\SmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use App\Models\ImapCredentials;
use App\Models\SmtpCredentials;

class OutboundMailController extends Controller
{
    public function sendEmail(Request $request, $credentialId)
    {
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $smtpCredentials = SmtpCredentials::where('imap_credential_id', $credentialId)->first();

        $transport = new EsmtpTransport('lucasvanbriemen.nl', 465);
        $transport->setUsername($smtpCredentials->username);
        $transport->setPassword($smtpCredentials->password);

        $events = app()->get('events');
        $viewFactory = app()->get('view');

        $mailer = new Mailer('test_mailer', $viewFactory, app()->get('mailer')->getSymfonyTransport(), $events);
        $mailer->setSymfonyTransport($transport);

        $mailer->alwaysFrom($smtpCredentials->from_email, $smtpCredentials->from_name);
        $mailer->alwaysReplyTo($smtpCredentials->from_email, $smtpCredentials->from_name);

        $mailer->send([], [], function ($message) use ($request) {
            $message->to($request->input('to'))
                ->subject($request->input('subject'))
                ->html($request->input('body'));
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Email sent successfully.',
        ]);
    }
}
