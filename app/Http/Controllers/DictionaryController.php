<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DictionaryController extends Controller
{
    /**
     * Display the main dictionary interface.
     */
    public function index(Request $request): View
    {
        $query = trim($request->input('q', ''));
        $selectedHsk = $request->has('hsk') && $request->input('hsk') !== '' ? (int) $request->input('hsk') : null;

        $initialResult = null;
        if ($query !== '') {
            $initialResult = $this->lookupWord($query, $selectedHsk);
        } else {
            // Default featured word (e.g., 你好 or 高兴)
            $defaultCard = Flashcard::where('is_active', true)
                ->where('hanzi', '你好')
                ->first()
                ?? Flashcard::where('is_active', true)->orderBy('id')->first();

            if ($defaultCard) {
                $initialResult = $this->formatWordData($defaultCard);
            }
        }

        // Quick suggested words for inspiration
        $suggestions = Flashcard::where('is_active', true)
            ->whereIn('hsk_level', [1, 2, 3])
            ->inRandomOrder()
            ->take(8)
            ->get(['id', 'hanzi', 'pinyin', 'meaning', 'hsk_level']);

        return view('dictionary.index', [
            'query'         => $query,
            'selectedHsk'   => $selectedHsk,
            'initialResult' => $initialResult,
            'suggestions'   => $suggestions,
        ]);
    }

    /**
     * Live search API endpoint for AJAX queries and autocomplete.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));
        $hsk = $request->has('hsk') && $request->input('hsk') !== '' ? (int) $request->input('hsk') : null;

        if ($query === '') {
            return response()->json([
                'success' => true,
                'results' => [],
                'exact'   => null,
            ]);
        }

        $detectedType = $this->detectQueryType($query);
        $candidates = $this->findCandidates($query, $detectedType, $hsk);

        $exact = null;
        if ($candidates->isNotEmpty()) {
            $firstCard = $candidates->first();
            $exact = $this->formatWordData($firstCard);
        }

        return response()->json([
            'success'       => true,
            'query'         => $query,
            'detected_type' => $detectedType,
            'total'         => $candidates->count(),
            'results'       => $candidates->take(12)->map(fn ($c) => [
                'id'        => $c->id,
                'hanzi'     => $c->hanzi,
                'pinyin'    => $c->pinyin,
                'meaning'   => $c->meaning,
                'hsk_level' => $c->hsk_level,
            ]),
            'exact'         => $exact,
        ]);
    }

    /**
     * Lookup a single word by exact or closest match.
     */
    protected function lookupWord(string $query, ?int $hsk = null): ?array
    {
        $detectedType = $this->detectQueryType($query);
        $candidates = $this->findCandidates($query, $detectedType, $hsk);

        if ($candidates->isEmpty()) {
            return null;
        }

        return $this->formatWordData($candidates->first());
    }

    /**
     * Detect if user input is Chinese (Hanzi), Pinyin, or Vietnamese / meaning.
     */
    protected function detectQueryType(string $query): string
    {
        // Check if string contains Chinese characters
        if (preg_match('/\p{Han}/u', $query)) {
            return 'hanzi';
        }

        // Check if string looks like pinyin with tone numbers or marks (or single latin word with no vietnamese diacritics)
        $clean = strtolower($query);
        if (preg_match('/^[a-z0-9\sāáǎàēéěèīíǐìōóǒòūúǔùǖǘǚǜ]+$/i', $clean) && !preg_match('/[đăâêôơư]/i', $clean)) {
            return 'pinyin';
        }

        return 'vietnamese';
    }

    /**
     * Search flashcards based on detected type.
     */
    protected function findCandidates(string $query, string $type, ?int $hsk = null)
    {
        $builder = Flashcard::query()->where('is_active', true);

        if ($hsk) {
            $builder->where('hsk_level', $hsk);
        }

        if ($type === 'hanzi') {
            // Sort exact match first, then prefix, then contains
            return $builder->where('hanzi', 'like', "%{$query}%")
                ->orderByRaw("CASE WHEN hanzi = ? THEN 1 WHEN hanzi LIKE ? THEN 2 ELSE 3 END", [$query, "{$query}%"])
                ->orderBy('hsk_level')
                ->take(15)
                ->get();
        }

        if ($type === 'pinyin') {
            $cleanPinyin = preg_replace('/[0-9\s]/', '', strtolower($query));
            $results = (clone $builder)->where(function ($q) use ($query, $cleanPinyin) {
                $q->where('pinyin', 'like', "%{$query}%")
                  ->orWhere('pinyin', 'like', "%{$cleanPinyin}%");
            })
            ->orderByRaw("CASE WHEN LOWER(pinyin) = ? THEN 1 WHEN LOWER(pinyin) LIKE ? THEN 2 ELSE 3 END", [strtolower($query), strtolower($query) . '%'])
            ->orderBy('hsk_level')
            ->take(15)
            ->get();

            // If direct accented match gave results, return them
            if ($results->isNotEmpty()) {
                return $results;
            }

            // Fallback: Tone-free pinyin matching (e.g. user typed "piaoliang", DB has "piàoliang")
            // Use % instead of _ because SQLite _ matches single bytes, not multibyte UTF-8 characters like à, ǐ, etc.
            $consonantPattern = preg_replace('/[aeiouü]+/i', '%', $cleanPinyin);
            $candidates = (clone $builder)->where('pinyin', 'like', "%{$consonantPattern}%")
                ->take(50)
                ->get();

            $toneFreeMatches = $candidates->filter(function ($c) use ($cleanPinyin) {
                $rawPinyin = self::removePinyinTones($c->pinyin);
                return str_contains($rawPinyin, $cleanPinyin);
            })->sortBy(function ($c) use ($cleanPinyin) {
                return self::removePinyinTones($c->pinyin) === $cleanPinyin ? 0 : 1;
            });

            if ($toneFreeMatches->isNotEmpty()) {
                return $toneFreeMatches->values();
            }

            return $candidates->take(15);
        }

        // Vietnamese / Meaning search
        return $builder->where(function ($q) use ($query) {
            $q->where('meaning', 'like', "%{$query}%")
              ->orWhere('example_meaning', 'like', "%{$query}%");
        })
        ->orderBy('hsk_level')
        ->take(15)
        ->get();
    }

    /**
     * Format flashcard data and scan personal story context.
     */
    protected function formatWordData(Flashcard $card): array
    {
        $user = Auth::guard('web')->user();
        $isStarred = false;
        if ($user) {
            $isStarred = $user->starredFlashcards()->where('flashcard_id', $card->id)->exists();
        }

        // Personal Context: Scan published Graded Reader stories where this hanzi appears
        $occurrences = $this->findOccurrencesInStories($card->hanzi);

        return [
            'id'              => $card->id,
            'hanzi'           => $card->hanzi,
            'pinyin'          => $card->pinyin,
            'meaning'         => $card->meaning,
            'hsk_level'       => $card->hsk_level,
            'example'         => $card->example,
            'example_pinyin'  => $card->example_pinyin,
            'example_meaning' => $card->example_meaning,
            'is_starred'      => $isStarred,
            'story_matches'   => $occurrences,
            'story_count'     => count($occurrences),
        ];
    }

    /**
     * Extract sentences containing the target Hanzi from Graded Reader stories.
     */
    protected function findOccurrencesInStories(string $hanzi): array
    {
        if (mb_strlen($hanzi) === 0) {
            return [];
        }

        $stories = Story::where('is_published', true)
            ->select(['id', 'title', 'title_vi', 'slug', 'hsk_level', 'content_json'])
            ->get();

        $matches = [];

        foreach ($stories as $story) {
            $content = $story->content_json;
            if (!is_array($content)) {
                continue;
            }

            foreach ($content as $sentenceIdx => $sentence) {
                $chineseText = $sentence['chinese'] ?? '';
                if (mb_strpos($chineseText, $hanzi) !== false) {
                    $matches[] = [
                        'story_id'        => $story->id,
                        'story_title'     => $story->title,
                        'story_title_vi'  => $story->title_vi,
                        'story_slug'      => $story->slug,
                        'hsk_level'       => $story->hsk_level,
                        'sentence_index'  => $sentenceIdx + 1,
                        'chinese'         => $chineseText,
                        'pinyin'          => $sentence['pinyin'] ?? '',
                        'vietnamese'      => $sentence['vietnamese'] ?? '',
                    ];

                    // Max 2 sentences per story, max 8 stories total for clean UI
                    if (count($matches) >= 8) {
                        break 2;
                    }
                }
            }
        }

        return $matches;
    }

    /**
     * Remove tone diacritics from Pinyin for tone-free search.
     */
    public static function removePinyinTones(string $str): string
    {
        $map = [
            'ā' => 'a', 'á' => 'a', 'ǎ' => 'a', 'à' => 'a',
            'ē' => 'e', 'é' => 'e', 'ě' => 'e', 'è' => 'e',
            'ī' => 'i', 'í' => 'i', 'ǐ' => 'i', 'ì' => 'i',
            'ō' => 'o', 'ó' => 'o', 'ǒ' => 'o', 'ò' => 'o',
            'ū' => 'u', 'ú' => 'u', 'ǔ' => 'u', 'ù' => 'u',
            'ǖ' => 'u', 'ǘ' => 'u', 'ǚ' => 'u', 'ǜ' => 'u', 'ü' => 'u',
            ' ' => '',
        ];
        return strtr(mb_strtolower($str), $map);
    }
}
