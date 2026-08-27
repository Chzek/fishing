<?php

namespace Fishinglog\Livewire\Components;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Lake;
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

    #[Url(history: true, as: 'sort_by')]
    public string $sortBy = '';

    #[Url(history: true, as: 'sort_order')]
    public string $sortOrder = 'asc';

    /**
     * Active sort criteria stack supporting multi-column sorting.
     * Example: [['column' => 'lake', 'direction' => 'asc'], ['column' => 'species', 'direction' => 'desc']]
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
        string $defaultSortOrder = 'asc'
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
        if (request()->has('sort_by') && !empty(request('sort_by'))) {
            $this->sortBy = (string) request('sort_by');
        }
        if (request()->has('sort_order') && !empty(request('sort_order'))) {
            $this->sortOrder = (string) request('sort_order');
        }

        if (empty($this->sortBy)) {
            $this->sortBy = $this->defaultSortBy;
            $this->sortOrder = $this->defaultSortOrder;
        }

        $this->sorts = [
            ['column' => $this->sortBy, 'direction' => $this->sortOrder]
        ];
    }

    /**
     * Tri-State Multi-Column Sorting Cycle:
     * - Regular Click: Reset multi-sort and cycle single column (Asc -> Desc -> Reset to default).
     * - Shift + Click: Add/toggle column in multi-sort stack without clearing existing columns.
     */
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
                    // Tri-State remove column from multi-sort stack
                    array_splice($this->sorts, $existingIndex, 1);
                }
            } else {
                $this->sorts[] = ['column' => $column, 'direction' => 'asc'];
            }
        } else {
            // Single Click: Reset multi-sort stack and cycle target column
            if (count($this->sorts) === 1 && $this->sorts[0]['column'] === $column) {
                if ($this->sorts[0]['direction'] === 'asc') {
                    $this->sorts = [['column' => $column, 'direction' => 'desc']];
                } else {
                    // Reset to empty / default sort
                    $this->sorts = [];
                }
            } else {
                $this->sorts = [['column' => $column, 'direction' => 'asc']];
            }
        }

        if (!empty($this->sorts)) {
            $this->sortBy = $this->sorts[0]['column'];
            $this->sortOrder = $this->sorts[0]['direction'];
        } else {
            $this->sortBy = $this->defaultSortBy;
            $this->sortOrder = $this->defaultSortOrder;
        }

        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->sorts = [['column' => $this->sortBy, 'direction' => $this->sortOrder ?: 'asc']];
    }

    public function updatedSortOrder(): void
    {
        $this->sorts = [['column' => $this->sortBy ?: $this->defaultSortBy, 'direction' => $this->sortOrder]];
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

    public function resetFilters(): void
    {
        $this->reset(['search']);
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

        // Apply Model-specific relation counts safely inside render
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
        } else {
            if (!empty($this->withCount)) {
                $query->withCount($this->withCount);
            }
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
                    $searchableCols = ['name', 'title', 'firstName', 'lastName'];
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

            if (in_array($sortColKey, ['records_count', 'visits', 'anglers_count', 'lakes_count'])) {
                $query->orderBy(DB::raw($sortColKey), $order);
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
