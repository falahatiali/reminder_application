<?php

use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\Register;
use App\Http\Livewire\Home;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();

Route::get('/home', Home::class)->name('home');
Route::get("/login" , Login::class)->name('login');
Route::get("/register" , Register::class)->name('register');
//Route::get("/reminders" , Reminder::class)->name('login');
