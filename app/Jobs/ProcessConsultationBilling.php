<?php

namespace App\Jobs;

use App\Enums\ConsultationStatus;
use App\Models\Consultation;
use App\Services\ConsultationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessConsultationBilling implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Consultation $consultation,
    ) {}

    public function handle(ConsultationService $service): void
    {
        $this->consultation->refresh();

        if ($this->consultation->status !== ConsultationStatus::Active) {
            return;
        }

        $success = $service->debitForMinute($this->consultation);

        // If debit succeeded and consultation is still active, schedule next billing
        if ($success && $this->consultation->fresh()->status === ConsultationStatus::Active) {
            self::dispatch($this->consultation)->delay(now()->addMinutes(1));
        }
    }
}
