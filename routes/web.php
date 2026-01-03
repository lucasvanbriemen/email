<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsLoggedIn;

Route::get('/{any}', function () {
    return view('spa');
})->middleware(IsLoggedIn::class)->where('any', '.*')->name('spa.catchall');
