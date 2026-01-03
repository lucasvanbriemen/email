<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsLoggedIn;
use App\Http\Controllers\MailboxController;

// Routes endpoint for frontend (no auth required)
Route::get('/routes', function () {
    return collect(Route::getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'method' => $route->methods()[0] ?? 'GET',
        ];
    })->values();
});

Route::middleware(IsLoggedIn::class)->group(function () {
    Route::get('/mailbox/metadata', [MailboxController::class, 'metadata'])->name('mailbox.metadata');
    Route::get('/mailbox/{group}', [MailboxController::class, 'emails'])->name('mailbox.emails');
    Route::get('/email/{uuid}', [MailboxController::class, 'email'])->name('email.view');
});
