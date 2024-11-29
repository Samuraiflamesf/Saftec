<?php

namespace App\Filament\Resources\StabilityConsultationResource\Pages;

use App\Filament\Resources\StabilityConsultationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStabilityConsultations extends ListRecords
{
    protected static string $resource = StabilityConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
