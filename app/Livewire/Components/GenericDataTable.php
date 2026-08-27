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

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true, as: 'sort_by')]
    public string $sortBy = '';

    #[Url(history: true, as: 'sort_order')]
    public string $sortOrder = 'asc';

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

        if (empty($this->sortBy)) {
            $this->sortBy = !empty($defaultSortBy) ? $defaultSortBy : ($columns[0]['key'] ?? 'id');
            $this->sortOrder = $defaultSortOrder;
        }

        if (request()->has('search') && empty($this->search)) {
            $this->search = (string) request('search');
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
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
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

    public function resetFilters(): void
    {
        $this->reset(['search']);
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

        // Apply Sorting
        $order = in_array(strtolower($this->sortOrder), ['asc', 'desc']) ? strtolower($this->sortOrder) : 'asc';
        
        if (!empty($this->sortBy)) {
            $sortColKey = $this->sortBy;
            foreach ($this->columns as $colDef) {
                if ($colDef['key'] === $this->sortBy && !empty($colDef['sortKey'])) {
                    $sortColKey = $colDef['sortKey'];
                    break;
                }
            }

            if ($sortColKey === 'full_name' || ($sortColKey === 'name' && str_contains($this->modelClass, 'Angler'))) {
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
