<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChartAnalysis extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id',
        'birth_chart_hash',
        'engine_version',
        'analysis_type',
        'analysis_json',
        'free_summary_json',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'analysis_json' => 'array',
            'free_summary_json' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    public function scopeForChart($query, string $hash): void
    {
        $query->where('birth_chart_hash', $hash);
    }
}
