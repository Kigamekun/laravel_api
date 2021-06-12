<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiController;




Route::post('register', [ApiController::class,'register'])->name('register');
Route::post('login', [ApiController::class,'login'])->name('login');
Route::get('logout', [ApiController::class,'logout'])->name('logout');