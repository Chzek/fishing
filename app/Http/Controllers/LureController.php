<?php

namespace Fishinglog\Http\Controllers;

use Database\Seeders\LureSeeder;
use Fishinglog\Http\Requests\StoreLureRequest;
use Fishinglog\Http\Requests\UpdateLureRequest;
use Fishinglog\Models\Lure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(\Illuminate\Pipeline\Pipeline $pipeline, Request $request)
    {
        $query = Lure::query()->withCount('records');

        $activeCategory = $request->query('category', 'all');

        if ($activeCategory !== 'all' && !empty($activeCategory)) {
            $query->where('category', $activeCategory);
        }

        if (!$request->has('sort_by')) {
            $query->orderBy('name', 'asc');
        }

        $allLures = $query->get();

        // 2-Tier Nesting: Category -> Lure Model (Brand + Name) -> Color Variants
        $nestedTackle = $allLures->groupBy(function ($item) {
            return $item->category ?: 'Other';
        })->map(function ($categoryLures) {
            return $categoryLures->groupBy(function ($item) {
                $brandPrefix = $item->brand ? trim($item->brand) . ' ' : '';
                return $brandPrefix . trim($item->name);
            });
        });

        // Distinct category counts for Digital Tackle Box tabs
        $categoriesList = [
            'Crankbait',
            'Soft Plastic',
            'Swimbait',
            'Inline Spinner',
            'Spinnerbait',
            'Jig',
            'Spoon',
            'Topwater',
            'Fly',
            'Other',
        ];

        $categoryCounts = Lure::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        $totalTackleCount = Lure::count();
        $totalCatchesOnTackle = DB::table('records')->whereNotNull('lures_id')->whereNull('deleted_at')->count();

        $topCategory = Lure::select('category', DB::raw('count(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->first();

        return view('lure.index', [
            'allLures' => $allLures,
            'nestedTackle' => $nestedTackle,
            'activeCategory' => $activeCategory,
            'categoriesList' => $categoriesList,
            'categoryCounts' => $categoryCounts,
            'totalTackleCount' => $totalTackleCount,
            'totalCatchesOnTackle' => $totalCatchesOnTackle,
            'topCategoryName' => $topCategory?->category ?? 'Tackle Box',
        ]);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lure = new Lure;
        $categoriesList = ['Crankbait', 'Soft Plastic', 'Swimbait', 'Inline Spinner', 'Spinnerbait', 'Jig', 'Spoon', 'Topwater', 'Fly', 'Other'];
        $existingBrands = Lure::whereNotNull('brand')->distinct()->pluck('brand');

        return view('lure.create', [
            'lure' => $lure,
            'categoriesList' => $categoriesList,
            'existingBrands' => $existingBrands,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\StoreLureRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLureRequest $request)
    {
        $lure = new Lure;
        $lure->name = $request->name;
        $lure->brand = $request->brand;
        $lure->category = $request->category ?: 'Other';
        $lure->color = $request->color;
        $lure->size = $request->size;
        $lure->weight = $request->weight ?: $request->size;
        $lure->depth_range = $request->depth_range;

        $lure->save();

        return redirect('/lure')->with('status', "Tackle item '{$lure->displayName}' added to your tackle box!");
    }

    /**
     * Batch store multiple color variants for a single lure model.
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'colors_input' => 'required|string',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
            'size' => 'nullable|string',
            'weight' => 'nullable|string',
            'depth_range' => 'nullable|string',
        ]);

        $rawColors = explode(',', $request->colors_input);
        $createdCount = 0;

        foreach ($rawColors as $rawColor) {
            $color = trim($rawColor);
            if (!empty($color)) {
                Lure::firstOrCreate(
                    [
                        'name' => $request->name,
                        'color' => $color,
                        'size' => $request->size,
                    ],
                    [
                        'brand' => $request->brand,
                        'category' => $request->category ?: 'Other',
                        'weight' => $request->weight ?: $request->size,
                        'depth_range' => $request->depth_range,
                    ]
                );
                $createdCount++;
            }
        }

        return redirect('/lure')->with('status', "Created {$createdCount} color variant(s) for '{$request->name}'!");
    }

    /**
     * Quick-add lure via AJAX modal during catch logging.
     */
    public function storeQuick(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'color' => 'nullable|string',
            'category' => 'nullable|string',
            'brand' => 'nullable|string',
        ]);

        $lure = Lure::firstOrCreate(
            [
                'name' => $request->name,
                'color' => $request->color,
                'size' => $request->size,
            ],
            [
                'brand' => $request->brand,
                'category' => $request->category ?: 'Other',
                'weight' => $request->weight ?: $request->size,
                'depth_range' => $request->depth_range,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'lure' => [
                    'id' => $lure->id,
                    'name' => $lure->displayName,
                    'category' => $lure->category,
                ],
            ]);
        }

        return redirect()->back()->with('status', "Lure '{$lure->displayName}' added!");
    }

    /**
     * Trigger Master Tackle Catalog Import.
     */
    public function importCatalog()
    {
        $seeder = new LureSeeder;
        $seeder->run();

        return redirect('/lure')->with('status', 'Master Tackle Catalog imported successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Models\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function show(Lure $lure)
    {
        $lure->loadCount('records');
        $catches = $lure->records()->with(['angler', 'lake', 'fishBreed', 'photos'])->latest('caught')->paginate(12);

        $topSpecies = DB::table('records')
            ->join('fish_breeds', 'records.fish_breeds_id', '=', 'fish_breeds.id')
            ->where('records.lures_id', $lure->id)
            ->whereNull('records.deleted_at')
            ->select('fish_breeds.name', DB::raw('count(*) as count'))
            ->groupBy('fish_breeds.name')
            ->orderByDesc('count')
            ->first();

        return view('lure.show', [
            'lure' => $lure,
            'catches' => $catches,
            'topSpeciesName' => $topSpecies?->name ?? 'N/A',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Models\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function edit(Lure $lure)
    {
        $categoriesList = ['Crankbait', 'Soft Plastic', 'Swimbait', 'Inline Spinner', 'Spinnerbait', 'Jig', 'Spoon', 'Topwater', 'Fly', 'Other'];
        $existingBrands = Lure::whereNotNull('brand')->distinct()->pluck('brand');

        return view('lure.edit', [
            'lure' => $lure,
            'categoriesList' => $categoriesList,
            'existingBrands' => $existingBrands,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\UpdateLureRequest  $request
     * @param  \Fishinglog\Models\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLureRequest $request, Lure $lure)
    {
        $targetLure = Lure::find($request->id) ?? $lure;

        $targetLure->name = $request->name;
        $targetLure->brand = $request->brand;
        $targetLure->category = $request->category ?: 'Other';
        $targetLure->color = $request->color;
        $targetLure->size = $request->size;
        $targetLure->weight = $request->weight ?: $request->size;
        $targetLure->depth_range = $request->depth_range;

        $targetLure->save();

        return redirect('/lure/' . $targetLure->id)->with('status', 'Tackle details updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Models\Lure  $lure
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lure $lure)
    {
        $lure->delete();

        return redirect('/lure')->with('status', 'Lure removed from tackle box.');
    }
}
