<?php

namespace Fishinglog\Livewire\Directory;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Record;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class FishDirectory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $family = '';

    #[Url(history: true, as: 'view')]
    public string $viewMode = 'grid';

    #[Url(history: true, as: 'sort_by')]
    public string $sortBy = 'name';

    #[Url(history: true, as: 'sort_order')]
    public string $sortOrder = 'asc';

    #[Url(history: true)]
    public int $perPage = 12;

    public function mount(
        ?string $initialFamily = null,
        ?string $initialSearch = null,
        ?string $initialView = null
    ): void {
        if (!empty($initialFamily) && empty($this->family)) {
            $this->family = $initialFamily;
        }

        if (!empty($initialSearch) && empty($this->search)) {
            $this->search = $initialSearch;
        }

        if (!empty($initialView) && in_array($initialView, ['grid', 'table'])) {
            $this->viewMode = $initialView;
        }

        // Adjust default perPage based on viewMode if not explicitly set in URL
        if (!request()->has('perPage')) {
            $this->perPage = $this->viewMode === 'grid' ? 12 : 15;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFamily(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['grid', 'table'])) {
            $this->viewMode = $mode;
            $this->perPage = $mode === 'grid' ? 12 : 15;
            $this->resetPage();
        }
    }

    public function selectFamily(string $familyId): void
    {
        if ($this->family === $familyId) {
            $this->family = '';
        } else {
            $this->family = $familyId;
        }
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->family = '';
        $this->resetPage();
    }

    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortOrder = in_array($column, ['records_count', 'longest_record', 'heaviest_record']) ? 'desc' : 'asc';
        }
        $this->resetPage();
    }

    public function render(): View
    {
        $families = FishFamily::withCount('breeds')
            ->orderBy('name', 'asc')
            ->get();

        $totalBreedsCount = FishBreed::count();

        $query = FishBreed::with(['family'])
            ->withCount('records')
            ->addSelect([
                'longest_record' => Record::selectRaw('max(length)')
                    ->whereColumn('fish_breeds_id', 'fish_breeds.id'),
                'heaviest_record' => Record::selectRaw('max(weight)')
                    ->whereColumn('fish_breeds_id', 'fish_breeds.id'),
            ]);

        // Filter by biological family (by UUID or name)
        if (!empty($this->family)) {
            $fam = $this->family;
            $query->where(function ($q) use ($fam) {
                $q->where('fish_breeds.fish_families_id', $fam)
                  ->orWhereHas('family', fn($f) => $f->where('name', $fam));
            });
        }

        // Filter by search keyword
        if (!empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('fish_breeds.name', 'like', $term)
                  ->orWhereHas('family', fn($fq) => $fq->where('name', 'like', $term));
            });
        }

        // Apply sorting
        if ($this->sortBy === 'family' || $this->sortBy === 'family.name') {
            $query->join('fish_families', 'fish_breeds.fish_families_id', '=', 'fish_families.id')
                  ->orderBy('fish_families.name', $this->sortOrder)
                  ->select('fish_breeds.*');
        } elseif (in_array($this->sortBy, ['records_count', 'longest_record', 'heaviest_record'])) {
            $query->orderBy($this->sortBy, $this->sortOrder);
        } else {
            $query->orderBy('fish_breeds.name', $this->sortOrder);
        }

        /** @var LengthAwarePaginator $fishes */
        $fishes = $query->paginate($this->perPage);

        return view('livewire.directory.fish-directory', [
            'fishes' => $fishes,
            'families' => $families,
            'totalBreedsCount' => $totalBreedsCount,
        ]);
    }
}
