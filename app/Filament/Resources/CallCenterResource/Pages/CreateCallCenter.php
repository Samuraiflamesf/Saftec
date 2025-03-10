<?php

namespace App\Filament\Resources\CallCenterResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CallCenterResource;
use Filament\Actions\Action;

class CreateCallCenter extends CreateRecord
{
    protected static string $resource = CallCenterResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Adiciona o ID do usuário autenticado no campo 'created_by'
        $data['created_by'] = Auth::id();

        $data['estabelecimento_id'] = Auth::user()->estabelecimento_id;

        return $data;
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(null)
            ->requiresConfirmation()
            ->action(function () {
                $this->closeActionModal();
                $this->create();
            });
    }
}
