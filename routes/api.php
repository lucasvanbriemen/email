<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsLoggedIn;
use App\Http\Controllers\MailboxController;

Route::middleware(IsLoggedIn::class)->group(function () {
    Route::get('/mailbox/metadata', [MailboxController::class, 'metadata'])->name('mailbox.metadata');
    Route::get('/mailbox/{group}', [MailboxController::class, 'index'])->name('mailbox.emails');
    Route::get('/email/{uuid}', [MailboxController::class, 'show'])->name('email.view');
});

Route::get('404', function () {
    return response()->json(['message' => 'Endpoint not found.'], 404);
});
