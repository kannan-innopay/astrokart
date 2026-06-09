<?php

namespace App\Models;

use Database\Factories\AstrologerAvailabilityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['astrologer_id', 'day_of_week', 'start_time', 'end_time', 'is_active'])]
class AstrologerAvailability extends Model
{
    /** @use HasFactory<AstrologerAvailabilityFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }
}
