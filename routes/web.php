<?php

use Illuminate\Support\Facades\Route;
use Techive\Telebirr\Controllers\TelebirrController;

Route::group(['prefix' => 'telebirr', 'as' => 'telebirr.'], function () {
    Route::any('notify', [TelebirrController::class, 'notify'])->name('notify');
    Route::any('return', [TelebirrController::class, 'return'])->name('return');
});