<?php

namespace App\Models;

use App\Concerns\HasUuid;

use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'code'])]
class Language extends Model
{
    /** @use HasFactory<LanguageFactory> */
    use HasFactory, HasUuid;

    public function astrologers(): BelongsToMany
    {
        return $this->belongsToMany(Astrologer::class, 'astrologer_language')->withTimestamps();
    }
}
