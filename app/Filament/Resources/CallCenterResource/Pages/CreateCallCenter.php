<?php

namespace App\Filament\Resources\CallCenterResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CallCenterResource;

class CreateCallCenter extends CreateRecord
{
    protected static string $resource = CallCenterResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Adiciona o user_create_id ao array de dados antes de criar o registro
        $data['user_create_id'] = Auth::id();

        return $data;
    }
}
