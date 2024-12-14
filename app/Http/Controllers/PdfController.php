<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StabilityConsultation;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function __invoke(StabilityConsultation $stabilityConsultation)
    {
        return Pdf::loadView('pdf', ['record' => $stabilityConsultation])
            ->download($stabilityConsultation->protocol_number . '.pdf');
    }
}
