<?php

namespace App\Filament\Resources\StabilityConsultationResource\Pages;

use App\Filament\Resources\StabilityConsultationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStabilityConsultation extends EditRecord
{
    protected static string $resource = StabilityConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
