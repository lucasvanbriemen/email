<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailboxController;

Route::get('/', [MailboxController::class, 'index'])->middleware(['auth', 'verified'])->name('mailbox');
Route::get('{credential_id}/folder/{folder}', [MailboxController::class, 'index'])->middleware(['auth', 'verified'])->name('mailbox.folder');
Route::get('{credential_id}/folder/{folder}/mail/{uuid}', [MailboxController::class, 'show'])->middleware(['auth', 'verified'])->name('mailbox.folder.mail');

Route::get('{credential_id}/folder/{folder}', [MailboxController::class, 'index'])->middleware(['auth', 'verified'])->name('mailbox.folder');


Route::post('{credential_id}/folder/{folder}/mail/{uuid}/archive', [MailboxController::class, 'archive'])->middleware(['auth', 'verified'])->name('mailbox.folder.mail.archive');
Route::post('{credential_id}/folder/{folder}/mail/{uuid}/delete', [MailboxController::class, 'delete'])->middleware(['auth', 'verified'])->name('mailbox.folder.mail.delete');
Route::post('{credential_id}/folder/{folder}/mail/{uuid}/star', [MailboxController::class, 'star'])->middleware(['auth', 'verified'])->name('mailbox.folder.mail.star');

Route::post('{credential_id}/folder/{folder}/mail/{uuid}/archive_thread', [MailboxController::class, 'archiveThread'])->middleware(['auth', 'verified'])->name('mailbox.folder.mail.archive_thread');
Route::post('{credential_id}/folder/{folder}/mail/{uuid}/delete_thread', [MailboxController::class, 'deleteThread'])->middleware(['auth', 'verified'])->name('mailbox.folder.mail.delete_thread');
Route::post('{credential_id}/folder/{folder}/mail/{uuid}/star_thread', [MailboxController::class, 'starThread'])->middleware(['auth', 'verified'])->name('mailbox.folder.mail.star_thread');

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::patch('/account', [AccountController::class, 'update'])->name('profile.update');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('profile.destroy');
    Route::post('/account/credentials', [AccountController::class, 'storeImapCredentials'])->name('account.credentials.store');
});

require __DIR__.'/auth.php';
