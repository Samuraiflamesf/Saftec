<?php

use App\Livewire\CardsPage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::redirect('/login', '/admin/login');

Route::get('cards', CardsPage::class);
Route::redirect('/', '/cards');

Route::get('pdf/{stabilityConsultation}', PdfController::class)->name('pdf');
