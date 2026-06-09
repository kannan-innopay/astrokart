<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyForecast extends Model
{
    protected $fillable = [
        'user_id',
        'forecast_month',
        'forecast_json',
        'engine_version',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'forecast_month' => 'date',
            'forecast_json' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
