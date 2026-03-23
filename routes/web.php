<?php

use App\Services\Nyt\NytClient;
use Illuminate\Support\Facades\Route;

Route::get('/', function (NytClient $nytClient) {
    $books = $nytClient->getBestSellers([
        'published_date' => '2026-03-23'
    ]);

    return view('welcome', ['books' => $books]);
});
