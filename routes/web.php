<?php

use App\Services\Nyt\NytClient;
use Illuminate\Support\Facades\Route;

Route::get('/', function (NytClient $nytClient) {
    $overview = $nytClient->getOverview('2026-03-23');

    return view('welcome', ['overview' => $overview]);
});

Route::get('/list/{name}', function (string $name, NytClient $nytClient) {
    $listDetail = $nytClient->getList($name, '2026-03-23');

    return view('list', ['listDetail' => $listDetail]);
})->name('list.show');
