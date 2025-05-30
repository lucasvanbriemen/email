<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailboxController;

Route::get('/', [MailboxController::class, 'index'])->middleware(['auth', 'verified'])->name('mailbox');
Route::get('/folder/{folder}', [MailboxController::class, 'index'])->middleware(['auth', 'verified'])->name('mailbox.folder');
Route::get('/folder/{folder}/mail/{uid}', [MailboxController::class, 'show'])->middleware(['auth', 'verified'])->name('mailbox.folder.mail');


Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::patch('/account', [AccountController::class, 'update'])->name('profile.update');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('profile.destroy');
    Route::post('/account/credentials', [AccountController::class, 'storeImapCredentials'])->name('account.credentials.store');
});

require __DIR__.'/auth.php';
