<?php

use Illuminate\Support\Facades\Route;
use Developes\Slack\Http\Controllers\SlackController;

Route::prefix('slack')->group(function(){
    Route::post('webhook', [SlackController::class, 'webhook'])->name('slack.webhook');
});
