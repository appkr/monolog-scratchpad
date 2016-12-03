<?php

Route::get('/', function () {
    $message = "Hello Monolog";
    $context = ['foo' => 'bar'];
    Log::debug($message, $context);
    
    return view('welcome');
});
