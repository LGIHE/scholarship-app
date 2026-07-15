<?php

namespace App\Filament\Resources\CohortResource\Pages;

use App\Filament\Resources\CohortResource;
use App\Models\Cohort;
use Filament\Resources\Pages\CreateRecord;

class CreateCohort extends CreateRecord
{
    protected static string $resource = CohortResource::class;

    /**
     * After saving, enforce the single-active-cohort rule if is_active was set.
     */
    protected function afterCreate(): void
    {
        if ($this->record->is_active) {
            Cohort::activateOnly($this->record);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
