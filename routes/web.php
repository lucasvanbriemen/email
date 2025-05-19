<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailboxController;

Route::get('/', [MailboxController::class, 'index'])->name('mailbox');

Route::get('/folder/{folder}', [MailboxController::class, 'index'])->middleware(['auth', 'verified'])->name('mailbox.folder');

Route::get('/folder/{folder}/mail/{uid}', [MailboxController::class, 'show'])->middleware(['auth', 'verified'])->name('mailbox.folder.mail');

require __DIR__.'/auth.php';
