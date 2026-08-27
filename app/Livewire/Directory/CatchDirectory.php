<?php

namespace Fishinglog\Livewire\Directory;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CatchDirectory extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $species = '';

    #[Url(history: true)]
    public string $lake = '';

    #[Url(history: true)]
    public string $angler = '';

    #[Url(history: true)]
    public string $lengthOperator = '>';

    #[Url(history: true)]
    public string $length = '';

    #[Url(history: true, as: 'sort_by')]
    public string $sortBy = 'date';

    #[Url(history: true, as: 'sort_order')]
    public string $sortOrder = 'desc';

    public function mount(): void
    {
        if (request()->has('search') && empty($this->search)) {
            $this->search = (string) request('search');
        }
        if (request()->has('species') && empty($this->species)) {
            $this->species = (string) request('species');
        }
        if (request()->has('species_id') && empty($this->species)) {
            $this->species = (string) request('species_id');
        }
        if (request()->has('lake') && empty($this->lake)) {
            $this->lake = (string) request('lake');
        }
        if (request()->has('lake_id') && empty($this->lake)) {
            $this->lake = (string) request('lake_id');
        }
        if (request()->has('angler') && empty($this->angler)) {
            $this->angler = (string) request('angler');
        }
        if (request()->has('length') && empty($this->length)) {
            $this->length = (string) request('length');
        }
        if (request()->has('length_operator') && !empty(request('length_operator'))) {
            $this->lengthOperator = (string) request('length_operator');
        }
        if (request()->has('sort_by') && !empty(request('sort_by'))) {
            $this->sortBy = (string) request('sort_by');
        }
        if (request()->has('sort_order') && !empty(request('sort_order'))) {
            $this->sortOrder = (string) request('sort_order');
        }
    }

    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            if ($this->sortOrder === 'asc') {
                $this->sortOrder = 'desc';
            } else {
                // Tri-State reset to default sort (date desc)
                $this->sortBy = 'date';
                $this->sortOrder = 'desc';
            }
        } else {
            $this->sortBy = $column;
            $this->sortOrder = 'asc';
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSpecies(): void
    {
        $this->resetPage();
    }

    public function updatedLake(): void
    {
        $this->resetPage();
    }

    public function updatedAngler(): void
    {
        $this->resetPage();
    }

    public function updatedLengthOperator(): void
    {
        $this->resetPage();
    }

    public function updatedLength(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function updatedSortOrder(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'species', 'lake', 'angler', 'length', 'lengthOperator', 'sortBy', 'sortOrder']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Record::with(['angler', 'lake.dailyWeather', 'fishBreed', 'lure']);

        if (!empty($this->search)) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('fishBreed', fn($b) => $b->where('name', 'like', $term))
                  ->orWhereHas('lake', fn($l) => $l->where('name', 'like', $term))
                  ->orWhereHas('angler', fn($a) => $a->where('firstName', 'like', $term)->orWhere('lastName', 'like', $term))
                  ->orWhereHas('lure', fn($lu) => $lu->where('name', 'like', $term));
            });
        }

        if (!empty($this->species)) {
            $spVal = $this->species;
            $query->where(function ($q) use ($spVal) {
                $q->where('fish_breeds_id', $spVal)
                  ->orWhereHas('fishBreed', fn($b) => $b->where('name', $spVal));
            });
        }

        if (!empty($this->lake)) {
            $lkVal = $this->lake;
            $query->where(function ($q) use ($lkVal) {
                $q->where('lakes_id', $lkVal)
                  ->orWhereHas('lake', fn($l) => $l->where('name', $lkVal));
            });
        }

        if (!empty($this->angler)) {
            $angVal = $this->angler;
            $query->where(function ($q) use ($angVal) {
                $q->where('anglers_id', $angVal)
                  ->orWhereHas('angler', fn($a) => $a->where('firstName', $angVal)->orWhere('lastName', $angVal));
            });
        }

        if ($this->length !== '' && $this->length !== null) {
            $op = in_array($this->lengthOperator, ['>', '=', '<']) ? $this->lengthOperator : '>';
            $query->where('length', $op, (float) $this->length);
        }

        $order = in_array(strtolower($this->sortOrder), ['asc', 'desc']) ? strtolower($this->sortOrder) : 'desc';

        switch ($this->sortBy) {
            case 'species':
                $query->leftJoin('fish_breeds', 'records.fish_breeds_id', '=', 'fish_breeds.id')
                      ->orderBy('fish_breeds.name', $order)
                      ->select('records.*');
                break;
            case 'lake':
                $query->leftJoin('lakes', 'records.lakes_id', '=', 'lakes.id')
                      ->orderBy('lakes.name', $order)
                      ->select('records.*');
                break;
            case 'angler':
                $query->leftJoin('anglers', 'records.anglers_id', '=', 'anglers.id')
                      ->orderBy('anglers.lastName', $order)
                      ->orderBy('anglers.firstName', $order)
                      ->select('records.*');
                break;
            case 'lure':
                $query->leftJoin('lures', 'records.lures_id', '=', 'lures.id')
                      ->orderBy('lures.name', $order)
                      ->select('records.*');
                break;
            case 'weight':
                $query->orderBy('weight', $order);
                break;
            case 'length':
                $query->orderBy('length', $order);
                break;
            case 'status':
                $query->orderBy('released', $order);
                break;
            case 'date':
            case 'caught':
            default:
                $query->orderBy('caught', $order);
                break;
        }

        $records = $query->paginate(15);
        $totalCount = $records->total();

        return view('livewire.directory.catch-directory', [
            'records' => $records,
            'totalCount' => $totalCount,
            'speciesList' => FishBreed::orderBy('name')->get(['id', 'name']),
            'lakesList' => Lake::orderBy('name')->get(['id', 'name']),
            'anglersList' => Angler::orderBy('lastName')->get(['id', 'firstName', 'lastName']),
        ]);
    }
}
