<?php

use App\Livewire\CardsPage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::redirect('/login', '/admin/login');
Route::redirect('/', '/cards');

/* Route::get('cards', CardsPage::class); */

Route::get('pdf/{stabilityConsultation}', PdfController::class)->name('pdf');
