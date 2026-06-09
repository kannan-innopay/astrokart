<?php

namespace App\Models;

use App\Concerns\HasUuid;

use Database\Factories\ExpertiseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'description', 'is_active'])]
class Expertise extends Model
{
    /** @use HasFactory<ExpertiseFactory> */
    use HasFactory, HasUuid;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function astrologers(): BelongsToMany
    {
        return $this->belongsToMany(Astrologer::class, 'astrologer_expertise')->withTimestamps();
    }
}
