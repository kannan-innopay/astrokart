<?php

namespace App\Models;

use App\Concerns\HasUuid;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'order_id', 'amount', 'status', 'payment_method', 'gateway_response', 'remarks'])]
class Payment extends Model
{
    use HasUuid;

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'gateway_response' => 'array',
            'amount' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
