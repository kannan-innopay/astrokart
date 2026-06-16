<?php

namespace App\Models;

use App\Enums\AstrologerDocumentType;
use Database\Factories\AstrologerDocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['astrologer_id', 'document_type', 'document_number', 'file_path', 'is_verified', 'verified_at', 'notes'])]
class AstrologerDocument extends Model
{
    /** @use HasFactory<AstrologerDocumentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'document_type' => AstrologerDocumentType::class,
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }
}
