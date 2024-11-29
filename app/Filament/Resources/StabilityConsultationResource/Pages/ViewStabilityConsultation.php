<?php

namespace App\Filament\Resources\StabilityConsultationResource\Pages;

use App\Filament\Resources\StabilityConsultationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStabilityConsultation extends ViewRecord
{
    protected static string $resource = StabilityConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
