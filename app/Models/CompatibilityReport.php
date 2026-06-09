<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompatibilityReport extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id',
        'partner_name',
        'partner_dob',
        'partner_moon_nakshatra',
        'partner_moon_rashi',
        'score',
        'result_json',
        'amount_charged',
        'wallet_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'partner_dob' => 'date',
            'partner_moon_nakshatra' => 'integer',
            'partner_moon_rashi' => 'integer',
            'score' => 'integer',
            'result_json' => 'array',
            'amount_charged' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
