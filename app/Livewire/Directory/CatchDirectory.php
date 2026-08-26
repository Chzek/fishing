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

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true, as: 'species')]
    public string $speciesId = '';

    #[Url(history: true, as: 'angler')]
    public string $anglerId = '';

    #[Url(history: true, as: 'lake')]
    public string $lakeId = '';

    #[Url(history: true, as: 'sort')]
    public string $sortBy = 'caught_desc';

    #[Url(history: true, as: 'released')]
    public bool $releasedOnly = false;

    public function mount(): void
    {
        if (request()->has('species') && empty($this->speciesId)) {
            $this->speciesId = (string) request('species');
        }
        if (request()->has('species_id') && empty($this->speciesId)) {
            $this->speciesId = (string) request('species_id');
        }
        if (request()->has('lake') && empty($this->lakeId)) {
            $this->lakeId = (string) request('lake');
        }
        if (request()->has('lake_id') && empty($this->lakeId)) {
            $this->lakeId = (string) request('lake_id');
        }
        if (request()->has('angler') && empty($this->anglerId)) {
            $this->anglerId = (string) request('angler');
        }
        if (request()->has('angler_id') && empty($this->anglerId)) {
            $this->anglerId = (string) request('angler_id');
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSpeciesId(): void
    {
        $this->resetPage();
    }

    public function updatedAnglerId(): void
    {
        $this->resetPage();
    }

    public function updatedLakeId(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function updatedReleasedOnly(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'speciesId', 'anglerId', 'lakeId', 'sortBy', 'releasedOnly']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Record::query()->with(['angler', 'lake', 'fishBreed', 'lure']);

        if (!empty($this->search)) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('fishBreed', fn($b) => $b->where('name', 'like', $term))
                  ->orWhereHas('lake', fn($l) => $l->where('name', 'like', $term))
                  ->orWhereHas('angler', fn($a) => $a->where('firstName', 'like', $term)->orWhere('lastName', 'like', $term));
            });
        }

        if (!empty($this->speciesId)) {
            $speciesVal = $this->speciesId;
            $query->where(function ($q) use ($speciesVal) {
                $q->where('fish_breeds_id', $speciesVal)
                  ->orWhereHas('fishBreed', fn($b) => $b->where('name', $speciesVal));
            });
        }

        if (!empty($this->anglerId)) {
            $anglerVal = $this->anglerId;
            $query->where(function ($q) use ($anglerVal) {
                $q->where('anglers_id', $anglerVal)
                  ->orWhereHas('angler', fn($a) => $a->where('firstName', $anglerVal)->orWhere('lastName', $anglerVal));
            });
        }

        if (!empty($this->lakeId)) {
            $lakeVal = $this->lakeId;
            $query->where(function ($q) use ($lakeVal) {
                $q->where('lakes_id', $lakeVal)
                  ->orWhereHas('lake', fn($l) => $l->where('name', $lakeVal));
            });
        }

        if ($this->releasedOnly) {
            $query->where('released', 1);
        }

        switch ($this->sortBy) {
            case 'length_desc':
                $query->orderBy('length', 'desc');
                break;
            case 'weight_desc':
                $query->orderBy('weight', 'desc');
                break;
            case 'caught_asc':
                $query->orderBy('caught', 'asc');
                break;
            case 'caught_desc':
            default:
                $query->orderBy('caught', 'desc');
                break;
        }

        $records = $query->paginate(12);

        return view('livewire.directory.catch-directory', [
            'records' => $records,
            'speciesList' => FishBreed::orderBy('name')->get(['id', 'name']),
            'anglersList' => Angler::orderBy('lastName')->get(['id', 'firstName', 'lastName']),
            'lakesList' => Lake::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
