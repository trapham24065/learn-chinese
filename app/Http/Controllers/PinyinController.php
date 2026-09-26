<?php

namespace App\Http\Controllers;

use App\Services\PinyinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PinyinController extends Controller
{
    public function __construct(
        protected PinyinService $pinyinService
    ) {}

    /**
     * Display the Pinyin Pronunciation Center.
     */
    public function index(Request $request): View
    {
        $initials = $this->pinyinService->getInitials();
        $finals = $this->pinyinService->getFinals();
        $matrix = $this->pinyinService->getMatrix();
        $toneRules = $this->pinyinService->getToneRules();
        $syllables = $this->pinyinService->getSyllables();

        return view('pinyin.index', [
            'initials'  => $initials,
            'finals'    => $finals,
            'matrix'    => $matrix,
            'toneRules' => $toneRules,
            'syllables' => $syllables,
        ]);
    }

    /**
     * Get details of a single syllable (4 tones, chars, meanings).
     */
    public function syllable(string $syllable): JsonResponse
    {
        $data = $this->pinyinService->getSyllable($syllable);

        if (!$data) {
            return response()->json([
                'error' => 'Syllable not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'syllable' => $data,
        ]);
    }

    /**
     * Search syllables, characters, or meanings.
     */
    public function search(Request $request): JsonResponse
    {
        $query = (string) $request->query('q', '');
        $results = $this->pinyinService->search($query);

        return response()->json([
            'query'   => $query,
            'total'   => count($results),
            'results' => array_slice($results, 0, 20),
        ]);
    }
}
