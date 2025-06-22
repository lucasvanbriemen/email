<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OutboundMailController;

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('{linked_profile_id}/folder/{folder}', [MailboxController::class, 'index'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder');
Route::get('{linked_profile_id}/folder/{folder}/mail/{uuid}', [MailboxController::class, 'show'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail');

Route::get('{linked_profile_id}/folder/{folder}', [MailboxController::class, 'index'])->middleware(['auth', 'verified'])->name('mailbox.folder');

Route::post('{linked_profile_id}/compose_email', [OutboundMailController::class, 'sendEmail'])->middleware(['auth', 'verified'])->name('mailbox.folder');

Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/read', [MailboxController::class, 'read'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail.read');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/unread', [MailboxController::class, 'unread'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail.unread');

Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/star', [MailboxController::class, 'star'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail.star');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/unstar', [MailboxController::class, 'unstar'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail.unstar');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/archive', [MailboxController::class, 'archive'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail.archive');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/delete', [MailboxController::class, 'delete'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail.delete');

Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/read_thread', [MailboxController::class, 'readThread'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail.read_thread');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/archive_thread', [MailboxController::class, 'archiveThread'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail.archive_thread');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/delete_thread', [MailboxController::class, 'deleteThread'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail.delete_thread');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/star_thread', [MailboxController::class, 'starThread'])->middleware(['auth', 'verified', 'update_last_activity'])->name('mailbox.folder.mail.star_thread');

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::get('/account/{linked_profile_id}', [AccountController::class, 'edit'])->name('account.edit.profile');

    Route::patch('/account', [AccountController::class, 'update'])->name('profile.update');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('profile.destroy');

    Route::post('/account/credentials', [AccountController::class, 'storeImapCredentials'])->name('account.credentials.store');
    Route::post('/account/smtp_credentials', [AccountController::class, 'storeSmtpCredentials'])->name('account.credentials.store.smtp');
});

require __DIR__.'/auth.php';
