<?php

namespace App\Filament\Resources\EstabelecimentoResource\Pages;

use App\Filament\Resources\EstabelecimentoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstabelecimento extends EditRecord
{
    protected static string $resource = EstabelecimentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
