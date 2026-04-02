<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;
use App\Livewire\BecomePartner;
use App\Livewire\CatererSearch;
use App\Livewire\AboutPage;
use App\Livewire\FAQPage;

Route::get('/', HomePage::class)->name('home');
Route::get('/become-partner', BecomePartner::class)->name('become-partner');
Route::get('/caterers', CatererSearch::class)->name('caterers');
Route::get('/about', AboutPage::class)->name('about');
Route::get('/faq', FAQPage::class)->name('faq');

// Auth Placeholders
Route::get('/login', function() { return 'Login Page'; })->name('login');
Route::get('/register', function() { return 'Register Page'; })->name('register');
