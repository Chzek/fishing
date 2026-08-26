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

    #[Url(history: true)]
    public string $speciesId = '';

    #[Url(history: true)]
    public string $anglerId = '';

    #[Url(history: true)]
    public string $lakeId = '';

    #[Url(history: true)]
    public string $sortBy = 'caught_desc';

    #[Url(history: true)]
    public bool $releasedOnly = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSpeciesId()
    {
        $this->resetPage();
    }

    public function updatingAnglerId()
    {
        $this->resetPage();
    }

    public function updatingLakeId()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function resetFilters()
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
            $query->where('fish_breeds_id', $this->speciesId);
        }

        if (!empty($this->anglerId)) {
            $query->where('anglers_id', $this->anglerId);
        }

        if (!empty($this->lakeId)) {
            $query->where('lakes_id', $this->lakeId);
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

        // Aggregate statistics using single selectRaw query
        $stats = (clone $query)->reorder()->selectRaw('
            COUNT(*) as total_catches,
            COALESCE(SUM(length), 0) as total_inches,
            COALESCE(AVG(length), 0) as avg_length,
            COALESCE(SUM(CASE WHEN released = 1 THEN 1 ELSE 0 END), 0) as released_count
        ')->first();

        $records = $query->paginate(12);

        return view('livewire.directory.catch-directory', [
            'records' => $records,
            'stats' => $stats,
            'speciesList' => FishBreed::orderBy('name')->get(['id', 'name']),
            'anglersList' => Angler::orderBy('lastName')->get(['id', 'firstName', 'lastName']),
            'lakesList' => Lake::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
