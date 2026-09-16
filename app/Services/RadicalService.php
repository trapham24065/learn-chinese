<?php

namespace App\Services;

use App\Models\Radical;
use App\Models\RadicalCharacter;
use Illuminate\Support\Collection;

class RadicalService
{
    /**
     * Find all radicals associated with the individual characters in a word or sentence.
     * Example: '你好' -> returns collection of Radical models for '亻' (from '你') and '女' (from '好').
     */
    public function getRadicalsForWord(string $hanzi): Collection
    {
        $hanzi = trim($hanzi);
        if ($hanzi === '') {
            return collect();
        }

        // Split into unique multibyte characters (ignore spaces and punctuation)
        $rawChars = mb_str_split($hanzi);
        $chineseChars = array_unique(array_filter($rawChars, function ($c) {
            return preg_match('/[\x{4e00}-\x{9fa5}]/u', $c);
        }));

        if (empty($chineseChars)) {
            return collect();
        }

        // 1. Look up characters in radical_characters table
        $radicalChars = RadicalCharacter::query()
            ->whereIn('character', $chineseChars)
            ->with('radical')
            ->get();

        $radicals = $radicalChars->pluck('radical')->filter();

        // 2. Also check if any character itself is directly a radical character or display_character
        $directRadicals = Radical::query()
            ->whereIn('character', $chineseChars)
            ->orWhereIn('display_character', $chineseChars)
            ->get();

        return $radicals->merge($directRadicals)
            ->unique('id')
            ->sortBy('sort_order')
            ->values();
    }

    /**
     * Get distribution of radicals by stroke count (1 to 17).
     */
    public function getStrokeDistribution(): array
    {
        return Radical::query()
            ->selectRaw('stroke_count, count(*) as count')
            ->groupBy('stroke_count')
            ->orderBy('stroke_count')
            ->pluck('count', 'stroke_count')
            ->toArray();
    }
}
