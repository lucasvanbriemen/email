<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsLoggedIn;
use App\Http\Middleware\AgentApiAuth;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\AgentApiController;

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
    Route::get('/mailbox/{group}', [MailboxController::class, 'index'])->name('mailbox.emails');
    Route::get('/email/{uuid}', [MailboxController::class, 'show'])->name('email.view');
});

Route::middleware(AgentApiAuth::class)->group(function () {
    Route::get('/emails/search', [AgentApiController::class, 'search'])->name('api.emails.search');
    Route::get('/emails/{id}', [AgentApiController::class, 'show'])->name('api.emails.show');
});

Route::get('404', function () {
    return response()->json(['message' => 'Endpoint not found.'], 404);
});
