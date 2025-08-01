<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OutboundMailController;

Route::get('/', [DashboardController::class, 'index'])->middleware(['is_logged_in'])->name('dashboard');

Route::get('{linked_profile_id}/folder/{folder}', [MailboxController::class, 'index'])->middleware(['is_logged_in'])->name('mailbox.overview');
Route::get('{linked_profile_id}/folder/{folder}/listing/{page?}', [MailboxController::class, 'getListingHTML'])->middleware(['is_logged_in'])->name('mailbox.folder.listing');

Route::get('{linked_profile_id}/folder/{folder}/mail/{uuid}', [MailboxController::class, 'show'])->middleware(['is_logged_in'])->name('mailbox.folder.mail');
Route::get('{linked_profile_id}/folder/{folder}/mail/{uuid}/html', [MailboxController::class, 'showHtml'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.html');

Route::post('{linked_profile_id}/compose_email', [OutboundMailController::class, 'sendEmail'])->middleware(['is_logged_in'])->name('mailbox.folder');

Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/read', [MailboxController::class, 'read'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.read');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/unread', [MailboxController::class, 'unread'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.unread');

Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/star', [MailboxController::class, 'star'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.star');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/unstar', [MailboxController::class, 'unstar'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.unstar');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/archive', [MailboxController::class, 'archive'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.archive');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/delete', [MailboxController::class, 'delete'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.delete');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/tag', [MailboxController::class, 'tag'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.delete');

Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/read_thread', [MailboxController::class, 'readThread'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.read_thread');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/archive_thread', [MailboxController::class, 'archiveThread'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.archive_thread');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/delete_thread', [MailboxController::class, 'deleteThread'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.delete_thread');
Route::post('{linked_profile_id}/folder/{folder}/mail/{uuid}/star_thread', [MailboxController::class, 'starThread'])->middleware(['is_logged_in'])->name('mailbox.folder.mail.star_thread');

Route::get('/is_loggedin', function() { 
    return response()->json(currentUser()->name);
})->middleware(['is_logged_in'])->name('mailbox.search');

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::get('/account/{linked_profile_id}', [AccountController::class, 'edit'])->name('account.edit.profile');

    Route::post('/account/{linked_profile_id}/imap', [AccountController::class, 'storeImapCredentials'])->name('account.credentials.store.imap');
    Route::post('/account/{linked_profile_id}/smtp', [AccountController::class, 'storeSmtpCredentials'])->name('account.credentials.store.smtp');
});