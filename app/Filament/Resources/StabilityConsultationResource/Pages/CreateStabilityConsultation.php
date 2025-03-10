<?php

namespace App\Filament\Resources\StabilityConsultationResource\Pages;

use App\Filament\Resources\StabilityConsultationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\CallCenterResource;
use Filament\Actions\Action;

class CreateStabilityConsultation extends CreateRecord
{
    protected static string $resource = StabilityConsultationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Adiciona o user_create_id ao array de dados antes de criar o registro
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
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [25, 50, 100];
    }
}
