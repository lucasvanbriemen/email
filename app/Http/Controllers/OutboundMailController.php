<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Transport\SmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class OutboundMailController extends Controller
{
    public function test()
    {
        $transport = new EsmtpTransport('lucasvanbriemen.nl', 465);
        $transport->setUsername('development@lucasvanbriemen.nl');
        $transport->setPassword('13November.2006');

        $events = app()->get('events');
        $viewFactory = app()->get('view');

        $mailer = new Mailer('test_mailer', $viewFactory, app()->get('mailer')->getSymfonyTransport(), $events);
        $mailer->setSymfonyTransport($transport);

        $mailer->alwaysFrom("development@lucasvanbriemen.nl", "Lucas van Briemen");
        $mailer->alwaysReplyTo("development@lucasvanbriemen.nl", "Lucas van Briemen");

        $mailer->send('auth.login', ['name' => 'Lucas'], function ($message) {
            $message->to('contact@lucasvanbriemen.nl', 'Lucas van Briemen')
                ->subject('Test Email');
        });

        return response('SMTP transport initialized.');
    }
}
