<?php

namespace App\Jobs;

use App\Enums\ConsultationStatus;
use App\Events\ConsultationRejected;
use App\Models\Consultation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ConsultationTimeout implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Consultation $consultation,
    ) {}

    public function handle(): void
    {
        $this->consultation->refresh();

        if ($this->consultation->status !== ConsultationStatus::Pending) {
            return;
        }

        $this->consultation->update([
            'status' => ConsultationStatus::Rejected,
            'ended_by' => 'system',
            'end_reason' => 'Request timed out — astrologer did not respond.',
        ]);

        ConsultationRejected::dispatch($this->consultation);
    }
}
