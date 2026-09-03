<?php

namespace Fishinglog\Livewire\Tacklebox;

use Fishinglog\Actions\Lures\CreateLureVariantAction;
use Fishinglog\Models\Lure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * @property-read array<int, string> $standardCategories
 */
class LureCatalog extends Component
{
    public string $search = '';
    public string $selectedCategory = 'all';
    public string $selectedDepth = 'all';
    public string $selectedBrand = 'all';

    /**
     * @var array<string, bool>
     */
    public array $openCategories = [];

    /**
     * @var array<string, bool>
     */
    public array $openModels = [];

    public bool $allExpanded = true;

    // Inline Add Colorway Variant Modal State
    public bool $showAddVariantModal = false;
    public string $targetModelBrand = '';
    public string $targetModelName = '';
    public string $targetModelCategory = 'Other';
    public ?string $targetModelDepth = null;
    public ?string $targetModelWeight = null;
    public string $newVariantColors = '';
    public string $newVariantSize = '';

    public ?string $statusMessage = null;

    /**
     * @var array<string, mixed>
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['as' => 'category', 'except' => 'all'],
        'selectedDepth' => ['as' => 'depth', 'except' => 'all'],
        'selectedBrand' => ['as' => 'brand', 'except' => 'all'],
    ];

    public function mount(?string $initialCategory = null): void
    {
        if (!empty($initialCategory) && $initialCategory !== 'all') {
            $this->selectedCategory = $initialCategory;
        }

        // Initialize all categories as open by default
        foreach ($this->getStandardCategoriesProperty() as $cat) {
            $this->openCategories[$cat] = true;
        }
    }

    public function updatedSearch(): void
    {
        // When searching, expand all categories so results are immediately visible
        if (!empty($this->search)) {
            foreach ($this->openCategories as $key => $val) {
                $this->openCategories[$key] = true;
            }
        }
    }

    public function toggleCategory(string $category): void
    {
        $this->openCategories[$category] = !($this->openCategories[$category] ?? true);
    }

    public function toggleModel(string $modelKey): void
    {
        $this->openModels[$modelKey] = !($this->openModels[$modelKey] ?? false);
    }

    public function toggleAllTrays(): void
    {
        $this->allExpanded = !$this->allExpanded;
        foreach ($this->getStandardCategoriesProperty() as $cat) {
            $this->openCategories[$cat] = $this->allExpanded;
        }
    }

    public function setCategory(string $category): void
    {
        $this->selectedCategory = $category;
    }

    public function setDepth(string $depth): void
    {
        $this->selectedDepth = $depth;
    }

    public function setBrand(string $brand): void
    {
        $this->selectedBrand = $brand;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = 'all';
        $this->selectedDepth = 'all';
        $this->selectedBrand = 'all';
    }

    public function logCatchWithLure(string $lureId): void
    {
        $this->dispatch('open-quick-catch', lure_id: $lureId);
    }

    public function openAddVariantModal(
        string $brand,
        string $name,
        string $category,
        ?string $depthRange = null,
        ?string $weight = null
    ): void {
        $this->targetModelBrand = $brand;
        $this->targetModelName = $name;
        $this->targetModelCategory = $category;
        $this->targetModelDepth = $depthRange;
        $this->targetModelWeight = $weight;
        $this->newVariantColors = '';
        $this->newVariantSize = $weight ?? '';
        $this->statusMessage = null;
        $this->showAddVariantModal = true;
    }

    public function closeAddVariantModal(): void
    {
        $this->showAddVariantModal = false;
        $this->newVariantColors = '';
        $this->statusMessage = null;
    }

    public function saveVariant(CreateLureVariantAction $action): void
    {
        $this->validate([
            'newVariantColors' => 'required|string|min:2',
            'targetModelName' => 'required|string',
        ]);

        $attributes = [
            'name' => $this->targetModelName,
            'brand' => $this->targetModelBrand ?: null,
            'category' => $this->targetModelCategory ?: 'Other',
            'depth_range' => $this->targetModelDepth ?: null,
            'size' => $this->newVariantSize ?: null,
            'weight' => $this->targetModelWeight ?: ($this->newVariantSize ?: null),
        ];

        $created = $action->execute($attributes, $this->newVariantColors);

        $this->statusMessage = sprintf('Added %d new colorway variant(s) to %s!', $created->count(), $this->targetModelName);
        $this->showAddVariantModal = false;
        $this->newVariantColors = '';
    }

    public function deleteVariant(string $lureId): void
    {
        $lure = Lure::find($lureId);
        if ($lure) {
            $name = $lure->displayName;
            $lure->delete();
            $this->statusMessage = sprintf('Deleted variant "%s" from tackle box.', $name);
        }
    }

    /**
     * @return array<int, string>
     */
    public function getStandardCategoriesProperty(): array
    {
        return [
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
    }

    public function render(): View
    {
        $query = Lure::query()->withCount('records');

        // Category filter
        if ($this->selectedCategory !== 'all' && !empty($this->selectedCategory)) {
            $query->where('category', $this->selectedCategory);
        }

        // Brand filter
        if ($this->selectedBrand !== 'all' && !empty($this->selectedBrand)) {
            $query->where('brand', $this->selectedBrand);
        }

        // Depth range filter
        if ($this->selectedDepth !== 'all') {
            switch ($this->selectedDepth) {
                case 'surface_0':
                    $query->where(function ($q) {
                        $q->where('depth_range', 'like', '%0%')
                            ->orWhere('depth_range', 'like', '%surface%')
                            ->orWhere('depth_range', 'like', '%top%')
                            ->orWhere('category', 'Topwater');
                    });
                    break;
                case 'shallow_1_5':
                    $query->where(function ($q) {
                        $q->where('depth_range', 'like', '%1%')
                            ->orWhere('depth_range', 'like', '%2%')
                            ->orWhere('depth_range', 'like', '%3%')
                            ->orWhere('depth_range', 'like', '%4%')
                            ->orWhere('depth_range', 'like', '%5%');
                    });
                    break;
                case 'mid_6_10':
                    $query->where(function ($q) {
                        $q->where('depth_range', 'like', '%6%')
                            ->orWhere('depth_range', 'like', '%7%')
                            ->orWhere('depth_range', 'like', '%8%')
                            ->orWhere('depth_range', 'like', '%9%')
                            ->orWhere('depth_range', 'like', '%10%');
                    });
                    break;
                case 'deep_10_20':
                    $query->where(function ($q) {
                        $q->where('depth_range', 'like', '%10%')
                            ->orWhere('depth_range', 'like', '%12%')
                            ->orWhere('depth_range', 'like', '%14%')
                            ->orWhere('depth_range', 'like', '%15%')
                            ->orWhere('depth_range', 'like', '%16%')
                            ->orWhere('depth_range', 'like', '%18%')
                            ->orWhere('depth_range', 'like', '%20%');
                    });
                    break;
                case 'deep_20_plus':
                    $query->where(function ($q) {
                        $q->where('depth_range', 'like', '%20+%')
                            ->orWhere('depth_range', 'like', '%25%')
                            ->orWhere('depth_range', 'like', '%30%')
                            ->orWhere('depth_range', 'like', '%deep%');
                    });
                    break;
            }
        }

        // Real-time search filter
        if (!empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('brand', 'like', $term)
                    ->orWhere('color', 'like', $term)
                    ->orWhere('size', 'like', $term)
                    ->orWhere('weight', 'like', $term)
                    ->orWhere('category', 'like', $term)
                    ->orWhere('depth_range', 'like', $term);
            });
        }

        $query->orderBy('name', 'asc');
        $allLures = $query->get();

        // 2-Tier Grouping: Category -> Model Name (Brand + Name) -> Collection of Variants
        $nestedTackle = $allLures->groupBy(function ($item) {
            return $item->category ?: 'Other';
        })->map(function ($categoryLures) {
            return $categoryLures->groupBy(function ($item) {
                $brandPrefix = $item->brand ? trim($item->brand) . ' ' : '';
                return $brandPrefix . trim($item->name);
            });
        });

        // Dynamic stats
        $totalTackleCount = Lure::count();
        $totalCatchesOnTackle = DB::table('records')->whereNotNull('lures_id')->whereNull('deleted_at')->count();
        $categoryCounts = Lure::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        $topCategory = Lure::select('category', DB::raw('count(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('count')
            ->first();

        $brandsList = Lure::whereNotNull('brand')->where('brand', '!=', '')->distinct()->orderBy('brand')->pluck('brand');

        return view('livewire.tacklebox.lure-catalog', [
            'nestedTackle' => $nestedTackle,
            'totalTackleCount' => $totalTackleCount,
            'totalCatchesOnTackle' => $totalCatchesOnTackle,
            'categoryCounts' => $categoryCounts,
            'topCategoryName' => $topCategory ? $topCategory->category : 'Crankbait',
            'brandsList' => $brandsList,
            'matchedCount' => $allLures->count(),
        ]);
    }
}
