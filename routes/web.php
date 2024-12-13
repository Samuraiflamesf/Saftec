<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::redirect('/', '/admin/login');
Route::get('pdf/{stabilityConsultation}', PdfController::class)->name('pdf');
