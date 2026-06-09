<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyPrediction extends Model
{
    protected $fillable = [
        'user_id',
        'prediction_date',
        'prediction_json',
        'engine_version',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'prediction_date' => 'date',
            'prediction_json' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
