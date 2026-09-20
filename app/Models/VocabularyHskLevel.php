<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularyHskLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'vocabulary_id',
        'hsk_standard_id',
        'level',
        'source',
        'topic',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'level'      => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function vocabulary(): BelongsTo
    {
        return $this->belongsTo(Vocabulary::class);
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(HskStandard::class, 'hsk_standard_id');
    }
}
