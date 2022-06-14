<?php

use App\Http\Controllers\Message\MessageController;



Route::group(['namespace' => 'Message'], function () {
    Route::middleware('auth')->group(function () {
        // views
        Route::group(['prefix' => '/chat'], function () {
            Route::get('/', function () {
                return view('message.index');
            });
            Route::get('/result', function () {
                return view('message.result');
            });
        });
        // api
        Route::group(['prefix' => 'api/message'], function () {
            Route::post('/send', [MessageController::class, 'send']);
        });
    });
});
