<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Keep this file minimal. Split routes into:
| - routes/frontoffice.php
| - routes/Backoffice.php
|--------------------------------------------------------------------------
*/

// Language switcher
Route::post('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['fr', 'en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('locale.switch');

require __DIR__ . '/frontoffice.php';
require __DIR__ . '/Backoffice.php';
