<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response('Bacapedia API — Sistem Manajemen Perpustakaan Digital', 200)
        ->header('Content-Type', 'text/plain');
});
