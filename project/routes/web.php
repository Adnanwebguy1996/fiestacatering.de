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

// Auth Routes
Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
Route::get('/caterers/{id}', \App\Livewire\CatererDetails::class)->name('caterer.details');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', \App\Livewire\User\Dashboard::class)->name('user.dashboard');
    Route::get('/dashboard/trucks/create', \App\Livewire\User\TruckForm::class)->name('user.trucks.create');
    Route::get('/admin', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');

    Route::get('/logout', function() {
        \Illuminate\Support\Facades\Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

