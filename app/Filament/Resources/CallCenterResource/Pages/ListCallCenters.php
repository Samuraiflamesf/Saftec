<?php

namespace App\Filament\Resources\CallCenterResource\Pages;

use App\Filament\Resources\CallCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCallCenters extends ListRecords
{
    protected static string $resource = CallCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
