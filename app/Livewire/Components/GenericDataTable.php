<?php

namespace Fishinglog\Livewire\Components;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class GenericDataTable extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $modelClass = '';

    public array $columns = [];

    public array $with = [];

    public array $withCount = [];

    public string $searchPlaceholder = 'Search...';

    public string $itemName = 'items';

    public int $perPage = 15;

    public string $defaultSortBy = 'id';

    public string $defaultSortOrder = 'asc';

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $family = '';

    #[Url(history: true)]
    public string $species = '';

    #[Url(history: true)]
    public string $lake = '';

    #[Url(history: true)]
    public string $angler = '';

    #[Url(history: true)]
    public string $lure = '';

    #[Url(history: true, as: 'sort_by')]
    public string $sortBy = '';

    #[Url(history: true, as: 'sort_order')]
    public string $sortOrder = 'asc';

    /**
     * Active sort criteria stack supporting multi-column sorting.
     */
    public array $sorts = [];

    public function mount(
        string $modelClass,
        array $columns,
        array $with = [],
        array $withCount = [],
        string $searchPlaceholder = 'Search...',
        string $itemName = 'items',
        int $perPage = 15,
        string $defaultSortBy = '',
        string $defaultSortOrder = 'asc',
        string $family = '',
        string $species = '',
        string $lake = '',
        string $angler = '',
        string $lure = ''
    ): void {
        $this->modelClass = $modelClass;
        $this->columns = $columns;
        $this->with = $with;
        $this->withCount = $withCount;
        $this->searchPlaceholder = $searchPlaceholder;
        $this->itemName = $itemName;
        $this->perPage = $perPage;

        $this->defaultSortBy = !empty($defaultSortBy) ? $defaultSortBy : ($columns[0]['key'] ?? 'id');
        $this->defaultSortOrder = $defaultSortOrder;

        if (request()->has('search') && empty($this->search)) {
            $this->search = (string) request('search');
        }

        // Support URL pre-filters from external incoming links or component params
        if (!empty($family)) {
            $this->family = $family;
        } elseif (request()->has('family') && empty($this->family)) {
            $this->family = (string) request('family');
        }

        if (!empty($species)) {
            $this->species = $species;
        } elseif (request()->has('species') && empty($this->species)) {
            $this->species = (string) request('species');
        }

        if (!empty($lake)) {
            $this->lake = $lake;
        } elseif (request()->has('lake') && empty($this->lake)) {
            $this->lake = (string) request('lake');
        }

        if (!empty($angler)) {
            $this->angler = $angler;
        } elseif (request()->has('angler') && empty($this->angler)) {
            $this->angler = (string) request('angler');
        }

        if (!empty($lure)) {
            $this->lure = $lure;
        } elseif (request()->has('lure') && empty($this->lure)) {
            $this->lure = (string) request('lure');
        }

        if (request()->has('sort_by') && !empty(request('sort_by'))) {
            $this->sortBy = (string) request('sort_by');
            $this->sortOrder = (string) request('sort_order', 'asc');

            $cols = explode(',', $this->sortBy);
            $dirs = explode(',', $this->sortOrder);
            $this->sorts = [];
            foreach ($cols as $idx => $col) {
                $dir = $dirs[$idx] ?? $dirs[0] ?? 'asc';
                $this->sorts[] = ['column' => trim($col), 'direction' => trim($dir)];
            }
        }

        if (empty($this->sorts)) {
            $this->sortBy = $this->defaultSortBy;
            $this->sortOrder = $this->defaultSortOrder;
            $this->sorts = [
                ['column' => $this->defaultSortBy, 'direction' => $this->defaultSortOrder]
            ];
        }
    }

    public function sortByColumn(string $column, bool $isShift = false): void
    {
        if ($isShift) {
            $existingIndex = null;
            foreach ($this->sorts as $index => $sort) {
                if ($sort['column'] === $column) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== null) {
                if ($this->sorts[$existingIndex]['direction'] === 'asc') {
                    $this->sorts[$existingIndex]['direction'] = 'desc';
                } else {
                    array_splice($this->sorts, $existingIndex, 1);
                }
            } else {
                $this->sorts[] = ['column' => $column, 'direction' => 'asc'];
            }
        } else {
            if (count($this->sorts) === 1 && $this->sorts[0]['column'] === $column) {
                if ($this->sorts[0]['direction'] === 'asc') {
                    $this->sorts = [['column' => $column, 'direction' => 'desc']];
                } else {
                    $this->sorts = [];
                }
            } else {
                $this->sorts = [['column' => $column, 'direction' => 'asc']];
            }
        }

        $this->syncSortProperties();
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $cols = explode(',', $this->sortBy);
        $dirs = explode(',', $this->sortOrder ?: 'asc');
        $this->sorts = [];
        foreach ($cols as $idx => $col) {
            if (empty(trim($col))) continue;
            $dir = $dirs[$idx] ?? $dirs[0] ?? 'asc';
            $this->sorts[] = ['column' => trim($col), 'direction' => trim($dir)];
        }
    }

    public function updatedSortOrder(): void
    {
        $cols = explode(',', $this->sortBy ?: $this->defaultSortBy);
        $dirs = explode(',', $this->sortOrder);
        $this->sorts = [];
        foreach ($cols as $idx => $col) {
            if (empty(trim($col))) continue;
            $dir = $dirs[$idx] ?? $dirs[0] ?? 'asc';
            $this->sorts[] = ['column' => trim($col), 'direction' => trim($dir)];
        }
    }

    protected function syncSortProperties(): void
    {
        if (!empty($this->sorts)) {
            $this->sortBy = implode(',', array_column($this->sorts, 'column'));
            $this->sortOrder = implode(',', array_column($this->sorts, 'direction'));
        } else {
            $this->sortBy = $this->defaultSortBy;
            $this->sortOrder = $this->defaultSortOrder;
            $this->sorts = [
                ['column' => $this->defaultSortBy, 'direction' => $this->defaultSortOrder]
            ];
        }
    }

    public function getSortDirection(string $column): ?string
    {
        foreach ($this->sorts as $sort) {
            if ($sort['column'] === $column) {
                return $sort['direction'];
            }
        }
        return null;
    }

    public function getSortOrderIndex(string $column): ?int
    {
        if (count($this->sorts) <= 1) {
            return null;
        }
        foreach ($this->sorts as $index => $sort) {
            if ($sort['column'] === $column) {
                return $index + 1;
            }
        }
        return null;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFamily(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'family', 'species', 'lake', 'angler', 'lure']);
        $this->sortBy = $this->defaultSortBy;
        $this->sortOrder = $this->defaultSortOrder;
        $this->sorts = [
            ['column' => $this->defaultSortBy, 'direction' => $this->defaultSortOrder]
        ];
        $this->resetPage();
    }

    public function render()
    {
        if (empty($this->modelClass) || !class_exists($this->modelClass)) {
            throw new \InvalidArgumentException("Invalid model class provided to GenericDataTable.");
        }

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = forward_static_call([$this->modelClass, 'query']);

        if (!empty($this->with)) {
            $query->with($this->with);
        }

        // Apply Model-specific relation counts & computed aggregates safely
        if ($this->modelClass === Lake::class) {
            $query->withCount('records')
                  ->withCount(['records as visits' => function ($q) {
                      $q->select(DB::raw('count(distinct records.caught)'));
                  }])
                  ->withCount(['anglers as anglers_count' => function ($q) {
                      $q->select(DB::raw('count(distinct anglers.id)'));
                  }]);
        } elseif ($this->modelClass === Angler::class) {
            $query->withCount('records')
                  ->withCount(['records as lakes_count' => function ($q) {
                      $q->select(DB::raw('count(distinct records.lakes_id)'));
                  }]);
        } elseif ($this->modelClass === Expedition::class) {
            $query->withCount('posts', 'crews')
                  ->addSelect(['records_count' => Record::selectRaw('count(*)')
                      ->whereColumn('caught', '>=', 'expeditions.start')
                      ->whereColumn('caught', '<=', 'expeditions.finish')
                  ]);
        } elseif ($this->modelClass === FishBreed::class) {
            $query->with(['family'])
                  ->withCount('records')
                  ->addSelect(['longest_record' => Record::selectRaw('max(length)')
                      ->whereColumn('fish_breeds_id', 'fish_breeds.id')
                  ])
                  ->addSelect(['heaviest_record' => Record::selectRaw('max(weight)')
                      ->whereColumn('fish_breeds_id', 'fish_breeds.id')
                  ]);
        } else {
            if (!empty($this->withCount)) {
                $query->withCount($this->withCount);
            }
        }

        // Apply Pre-filters from URL (family, species, lake, angler, etc.)
        if (!empty($this->family) && $this->modelClass === FishBreed::class) {
            $famVal = $this->family;
            $query->where(function ($q) use ($famVal) {
                if (is_numeric($famVal)) {
                    $q->where('fish_breeds.fish_families_id', (int) $famVal);
                } else {
                    $q->whereHas('family', fn($f) => $f->where('name', $famVal));
                }
            });
        }

        // Apply Search Filter
        if (!empty($this->search)) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $searchableCols = [];
                foreach ($this->columns as $col) {
                    if ($col['searchable'] ?? false) {
                        $searchableCols[] = $col['key'];
                    }
                }

                if (empty($searchableCols)) {
                    $searchableCols = ['name', 'title', 'firstName', 'lastName', 'email', 'description'];
                }

                $first = true;
                foreach ($searchableCols as $colKey) {
                    if (str_contains($colKey, '.')) {
                        [$relation, $relField] = explode('.', $colKey, 2);
                        if ($first) {
                            $q->whereHas($relation, fn($r) => $r->where($relField, 'like', $term));
                            $first = false;
                        } else {
                            $q->orWhereHas($relation, fn($r) => $r->where($relField, 'like', $term));
                        }
                    } else {
                        if ($first) {
                            $q->where($colKey, 'like', $term);
                            $first = false;
                        } else {
                            $q->orWhere($colKey, 'like', $term);
                        }
                    }
                }
            });
        }

        // Apply Multi-Column Sorting
        $activeSorts = !empty($this->sorts) ? $this->sorts : [
            ['column' => $this->defaultSortBy, 'direction' => $this->defaultSortOrder]
        ];

        foreach ($activeSorts as $sort) {
            $sortColKey = $sort['column'];
            $order = in_array(strtolower($sort['direction']), ['asc', 'desc']) ? strtolower($sort['direction']) : 'asc';

            foreach ($this->columns as $colDef) {
                if ($colDef['key'] === $sortColKey && !empty($colDef['sortKey'])) {
                    $sortColKey = $colDef['sortKey'];
                    break;
                }
            }

            if ($sortColKey === 'catches') {
                $sortColKey = 'records_count';
            } elseif ($sortColKey === 'lakes') {
                $sortColKey = 'lakes_count';
            } elseif ($sortColKey === 'crew') {
                $sortColKey = 'crews_count';
            } elseif ($sortColKey === 'posts') {
                $sortColKey = 'posts_count';
            } elseif ($sortColKey === 'family' || $sortColKey === 'family.name') {
                $query->leftJoin('fish_families', 'fish_breeds.fish_families_id', '=', 'fish_families.id')
                      ->select('fish_breeds.*')
                      ->orderBy('fish_families.name', $order);
                continue;
            }

            if (in_array($sortColKey, ['records_count', 'visits', 'anglers_count', 'lakes_count', 'crews_count', 'posts_count', 'longest_record', 'heaviest_record'])) {
                $query->orderByRaw("`{$sortColKey}` {$order}");
            } elseif (in_array($sortColKey, ['full_name', 'angler', 'lastName']) || (str_contains($this->modelClass, 'Angler') && in_array($sortColKey, ['name', 'angler']))) {
                $query->orderBy('lastName', $order)->orderBy('firstName', $order);
            } else {
                $query->orderBy($sortColKey, $order);
            }
        }

        $records = $query->paginate($this->perPage);
        $totalCount = $records->total();

        return view('livewire.components.generic-data-table', [
            'records' => $records,
            'totalCount' => $totalCount,
        ]);
    }
}
