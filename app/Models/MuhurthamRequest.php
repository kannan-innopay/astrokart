<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MuhurthamRequest extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id',
        'purpose',
        'date_range_start',
        'date_range_end',
        'status',
        'amount_charged',
        'wallet_transaction_id',
        'result_json',
        'failure_reason',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'date_range_start' => 'date',
            'date_range_end' => 'date',
            'amount_charged' => 'integer',
            'result_json' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
