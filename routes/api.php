<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsLoggedIn;
use App\Http\Controllers\MailboxController;

Route::middleware(IsLoggedIn::class)->group(function () {
    Route::get('/mailbox/metadata', [MailboxController::class, 'metadata'])->name('mailbox.metadata');
});
