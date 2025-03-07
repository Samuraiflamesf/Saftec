<?php

namespace App\Observers;

use App\Models\StabilityConsultation;

class StabilityConsultationObserver
{
    /**
     * Handle the StabilityConsultation "created" event.
     */
    public function created(StabilityConsultation $stabilityConsultation): void
    {
        //
    }

    /**
     * Handle the StabilityConsultation "updated" event.
     */
    public function updated(StabilityConsultation $stabilityConsultation): void
    {
        //
    }

    /**
     * Handle the StabilityConsultation "deleted" event.
     */
    public function deleted(StabilityConsultation $stabilityConsultation): void
    {
        //
    }

    /**
     * Handle the StabilityConsultation "restored" event.
     */
    public function restored(StabilityConsultation $stabilityConsultation): void
    {
        //
    }

    /**
     * Handle the StabilityConsultation "force deleted" event.
     */
    public function forceDeleted(StabilityConsultation $stabilityConsultation): void
    {
        //
    }
}
