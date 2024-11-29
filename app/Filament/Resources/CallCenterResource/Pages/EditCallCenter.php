<?php

namespace App\Filament\Resources\CallCenterResource\Pages;

use App\Filament\Resources\CallCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCallCenter extends EditRecord
{
    protected static string $resource = CallCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
