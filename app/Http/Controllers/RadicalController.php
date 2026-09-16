<?php

namespace App\Http\Controllers;

use App\Models\Radical;
use App\Services\RadicalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RadicalController extends Controller
{
    public function __construct(
        protected RadicalService $radicalService
    ) {}

    /**
     * Display the 214 Kangxi radicals catalog with interactive filtering.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $selectedStrokes = $request->filled('strokes') ? (int) $request->query('strokes') : null;
        $onlyCommon = $request->boolean('common') || $request->query('filter') === 'common';
        $selectedPosition = trim((string) $request->query('position', ''));

        $query = Radical::query()->withCount('characters');

        // Text Search
        if ($search !== '') {
            $query->search($search);
        }

        // Common filter (Top 100)
        if ($onlyCommon) {
            $query->common();
        }

        // Stroke count filter
        if ($selectedStrokes !== null && $selectedStrokes >= 1 && $selectedStrokes <= 17) {
            $query->byStrokes($selectedStrokes);
        }

        // Position filter
        if ($selectedPosition !== '' && in_array($selectedPosition, ['left', 'right', 'top', 'bottom', 'surround', 'inside', 'standalone', 'other'])) {
            $query->byPosition($selectedPosition);
        }

        // Order by common rank if common filter, otherwise by sort_order
        if ($onlyCommon && $search === '') {
            $query->orderBy('common_rank');
        } else {
            $query->orderBy('sort_order');
        }

        $radicals = $query->get();

        // Statistics for filter badges
        $totalCount = Radical::count();
        $commonCount = Radical::where('is_common', true)->count();
        $strokeDistribution = $this->radicalService->getStrokeDistribution();

        $positionOptions = [
            'left'       => 'Bên trái (左)',
            'right'      => 'Bên phải (右)',
            'top'        => 'Ở trên (上)',
            'bottom'     => 'Ở dưới (下)',
            'surround'   => 'Bao quanh (包)',
            'inside'     => 'Bên trong (中)',
            'standalone' => 'Độc thể (独)',
        ];

        return view('radicals.index', [
            'radicals'           => $radicals,
            'search'             => $search,
            'selectedStrokes'    => $selectedStrokes,
            'onlyCommon'         => $onlyCommon,
            'selectedPosition'   => $selectedPosition,
            'totalCount'         => $totalCount,
            'commonCount'        => $commonCount,
            'strokeDistribution' => $strokeDistribution,
            'positionOptions'    => $positionOptions,
        ]);
    }

    /**
     * Display a specific radical details, HanziWriter canvas, stroke animation, and characters.
     */
    public function show(string $slug): View
    {
        $radical = Radical::query()
            ->where('slug', $slug)
            ->orWhere('character', $slug)
            ->orWhere('display_character', $slug)
            ->with(['characters' => function ($q) {
                $q->orderBy('is_featured', 'desc')
                  ->orderBy('hsk_level')
                  ->orderBy('sort_order');
            }])
            ->firstOrFail();

        // Previous and Next radicals for pagination
        $prevRadical = Radical::where('sort_order', '<', $radical->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first() ?? Radical::orderBy('sort_order', 'desc')->first();

        $nextRadical = Radical::where('sort_order', '>', $radical->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first() ?? Radical::orderBy('sort_order', 'asc')->first();

        // Count characters by HSK level
        $charactersByHsk = $radical->characters->groupBy(fn ($c) => $c->hsk_level ?: 'other');

        return view('radicals.show', [
            'radical'         => $radical,
            'prevRadical'     => $prevRadical,
            'nextRadical'     => $nextRadical,
            'charactersByHsk' => $charactersByHsk,
        ]);
    }
}
