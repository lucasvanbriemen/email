<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailboxController;

Route::get('/', MailboxController::class . '@index')->name('mailbox.index');
