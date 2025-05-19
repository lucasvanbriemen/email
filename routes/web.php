<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailboxController;

Route::get('/', [MailboxController::class, 'index'])->name('mailbox.index');
Route::get('/mail/{uid}', [MailboxController::class, 'show'])->middleware(['auth', 'verified'])->name('mail.show');

require __DIR__.'/auth.php';
