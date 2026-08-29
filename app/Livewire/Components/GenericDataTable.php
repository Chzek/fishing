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

    public bool $onlyTrashed = false;

    public string $expeditionId = '';

    public string $lureId = '';

    public string $categoryId = '';

    public string $fishingZoneId = '';

    public string $lakeId = '';

    #[Url(history: true, as: 'sort_by')]
    public string $sortBy = '';

    #[Url(history: true, as: 'sort_order')]
    public string $sortOrder = 'asc';

    /**
     * Active sort criteria stack supporting multi-column sorting.
     */
    public array $sorts = [];

    /**
     * Dynamic pluggable filter definitions.
     */
    public array $filters = [];

    /**
     * Dynamic filter state store tracked by Livewire.
     */
    #[Url(history: true)]
    public array $filterState = [];

    /**
     * Optional query scopes to apply to the model query.
     */
    public array $scopes = [];

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
        string $lure = '',
        bool $onlyTrashed = false,
        string $expeditionId = '',
        string $lureId = '',
        string $categoryId = '',
        string $fishingZoneId = '',
        string $lakeId = '',
        array $filters = [],
        array $scopes = []
    ): void {
        $this->modelClass = $modelClass;
        $this->columns = $columns;
        $this->with = $with;
        $this->withCount = $withCount;
        $this->searchPlaceholder = $searchPlaceholder;
        $this->itemName = $itemName;
        $this->perPage = $perPage;
        $this->onlyTrashed = $onlyTrashed;
        $this->filters = $filters;
        $this->scopes = $scopes;

        $this->expeditionId = $expeditionId;
        $this->lureId = $lureId;
        $this->categoryId = $categoryId;
        $this->fishingZoneId = $fishingZoneId;
        $this->lakeId = $lakeId;

        // Initialize pluggable filter default states
        foreach ($filters as $flt) {
            $fKey = $flt['key'] ?? null;
            if (!$fKey) continue;

            $fType = $flt['type'] ?? 'select';

            if ($fType === 'operator_number') {
                $opKey = $flt['operatorKey'] ?? ($fKey . 'Operator');
                $defaultOp = $flt['defaultOperator'] ?? '>';
                if (!isset($this->filterState[$opKey])) {
                    $this->filterState[$opKey] = (string) request($opKey, $defaultOp);
                }
                if (!isset($this->filterState[$fKey])) {
                    $this->filterState[$fKey] = (string) request($fKey, '');
                }
            } elseif ($fType === 'date_range') {
                $pKey = $flt['presetKey'] ?? ($fKey . 'Preset');
                $sKey = $flt['startKey'] ?? ($fKey . 'Start');
                $eKey = $flt['endKey'] ?? ($fKey . 'End');
                if (!isset($this->filterState[$pKey])) {
                    $this->filterState[$pKey] = (string) request($pKey, '');
                }
                if (!isset($this->filterState[$sKey])) {
                    $this->filterState[$sKey] = (string) request($sKey, '');
                }
                if (!isset($this->filterState[$eKey])) {
                    $this->filterState[$eKey] = (string) request($eKey, '');
                }
            } else {
                if (!isset($this->filterState[$fKey])) {
                    $this->filterState[$fKey] = (string) request($fKey, $flt['default'] ?? '');
                }
            }
        }

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

    public function updatedFilterState(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFamily(): void
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

    public function updatedLure(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'family', 'species', 'lake', 'angler', 'lure', 'filterState']);
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
        $query = $this->onlyTrashed
            ? forward_static_call([$this->modelClass, 'onlyTrashed'])
            : forward_static_call([$this->modelClass, 'query']);

        if (!empty($this->with)) {
            $query->with($this->with);
        }

        // Apply caller-provided model scopes
        foreach ($this->scopes as $scopeName => $scopeArgs) {
            if (is_int($scopeName) && is_string($scopeArgs)) {
                $query->{$scopeArgs}();
            } elseif (is_string($scopeName)) {
                if (is_array($scopeArgs)) {
                    $query->{$scopeName}(...$scopeArgs);
                } else {
                    $query->{$scopeName}($scopeArgs);
                }
            }
        }

        // Apply Model-specific relation counts & computed aggregates safely
        if ($this->modelClass === Lake::class) {
            $lakeCounts = [
                'records',
                'records as visits' => function ($q) {
                    $q->select(DB::raw('count(distinct records.caught)'));
                },
                'anglers as anglers_count' => function ($q) {
                    $q->select(DB::raw('count(distinct anglers.id)'));
                }
            ];
            $query->withCount(array_merge($this->withCount, $lakeCounts));
        } elseif ($this->modelClass === Angler::class) {
            $anglerCounts = [
                'records',
                'records as lakes_count' => function ($q) {
                    $q->select(DB::raw('count(distinct records.lakes_id)'));
                }
            ];
            $query->withCount(array_merge($this->withCount, $anglerCounts));
        } elseif ($this->modelClass === Expedition::class) {
            $query->withCount(array_merge($this->withCount, ['posts', 'crews']))
                  ->addSelect(['records_count' => Record::selectRaw('count(*)')
                      ->whereColumn('caught', '>=', 'expeditions.start')
                      ->whereColumn('caught', '<=', 'expeditions.finish')
                  ]);
        } elseif ($this->modelClass === FishBreed::class) {
            $query->with(['family'])
                  ->withCount(array_merge($this->withCount, ['records']))
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

        // Scope relation filters for sub-tables
        if (!empty($this->expeditionId)) {
            if ($this->modelClass === Record::class) {
                $exp = Expedition::find($this->expeditionId);
                if ($exp) {
                    $query->whereBetween('caught', [$exp->start, $exp->finish]);
                }
            } else {
                $query->where('expedition_id', $this->expeditionId);
            }
        }

        if (!empty($this->lureId)) {
            if ($this->modelClass === Record::class) {
                $query->where('lures_id', $this->lureId);
            } else {
                $query->where('lure_id', $this->lureId);
            }
        }

        if (!empty($this->categoryId)) {
            $query->where('lure_category_id', $this->categoryId);
        }

        if (!empty($this->fishingZoneId)) {
            $query->where('fishing_zone_id', $this->fishingZoneId);
        }

        if (!empty($this->lakeId)) {
            if ($this->modelClass === Record::class) {
                $query->where('lakes_id', $this->lakeId);
            } else {
                $query->where('lake_id', $this->lakeId);
            }
        }

        // Apply Pluggable Dynamic Filters
        foreach ($this->filters as $flt) {
            $fKey = $flt['key'] ?? null;
            if (!$fKey) continue;

            $fType = $flt['type'] ?? 'select';
            $col = $flt['column'] ?? $fKey;

            if ($fType === 'operator_number') {
                $opKey = $flt['operatorKey'] ?? ($fKey . 'Operator');
                $val = $this->filterState[$fKey] ?? null;
                $op = $this->filterState[$opKey] ?? ($flt['defaultOperator'] ?? '>');
                if ($val !== null && $val !== '') {
                    $operator = in_array($op, ['>', '=', '<', '>=', '<=']) ? $op : '>';
                    $query->where($col, $operator, (float) $val);
                }
            } elseif ($fType === 'date_range') {
                $pKey = $flt['presetKey'] ?? ($fKey . 'Preset');
                $sKey = $flt['startKey'] ?? ($fKey . 'Start');
                $eKey = $flt['endKey'] ?? ($fKey . 'End');
                $preset = $this->filterState[$pKey] ?? null;

                if ($preset === 'today') {
                    $query->whereDate($col, now()->today());
                } elseif ($preset === 'yesterday') {
                    $query->whereDate($col, now()->yesterday());
                } elseif ($preset === 'this_week') {
                    $query->where($col, '>=', now()->subDays(7)->toDateString());
                } elseif ($preset === 'this_month') {
                    $query->where($col, '>=', now()->subDays(30)->toDateString());
                } elseif ($preset === 'this_season') {
                    $query->whereYear($col, now()->year);
                } elseif ($preset === 'last_season') {
                    $query->whereYear($col, now()->year - 1);
                } elseif ($preset === 'custom') {
                    $start = $this->filterState[$sKey] ?? null;
                    $end = $this->filterState[$eKey] ?? null;
                    if ($start && $end) {
                        $query->whereBetween($col, [$start, $end]);
                    } elseif ($start) {
                        $query->where($col, '>=', $start);
                    } elseif ($end) {
                        $query->where($col, '<=', $end);
                    }
                }
            } elseif ($fType === 'weather_condition') {
                $val = $this->filterState[$fKey] ?? null;
                if ($val !== null && $val !== '') {
                    $query->whereExists(function ($sub) use ($val) {
                        $sub->select(DB::raw(1))
                            ->from('lake_daily_weather')
                            ->whereColumn('lake_daily_weather.lakes_id', 'records.lakes_id')
                            ->whereRaw('lake_daily_weather.date = DATE(records.caught)')
                            ->where('lake_daily_weather.weather_condition', 'like', '%' . $val . '%');
                    });
                }
            } elseif ($fType === 'pressure_trend') {
                $val = $this->filterState[$fKey] ?? null;
                if ($val !== null && $val !== '') {
                    $query->whereExists(function ($sub) use ($val) {
                        $sub->select(DB::raw(1))
                            ->from('lake_daily_weather')
                            ->whereColumn('lake_daily_weather.lakes_id', 'records.lakes_id')
                            ->whereRaw('lake_daily_weather.date = DATE(records.caught)')
                            ->where('lake_daily_weather.pressure_trend', $val);
                    });
                }
            } elseif ($fType === 'text') {
                $val = $this->filterState[$fKey] ?? null;
                if ($val !== null && $val !== '') {
                    $query->where($col, 'like', '%' . $val . '%');
                }
            } elseif ($fType === 'boolean') {
                $val = $this->filterState[$fKey] ?? null;
                if ($val !== null && $val !== '') {
                    $trueVal = $flt['trueValue'] ?? 1;
                    $query->where($col, $val ? $trueVal : 0);
                }
            } else { // select
                $val = $this->filterState[$fKey] ?? (isset($this->{$fKey}) && !empty($this->{$fKey}) ? $this->{$fKey} : null);
                if ($val !== null && $val !== '') {
                    if ($col instanceof \Closure) {
                        $col($query, $val);
                    } elseif (is_string($col) && str_contains($col, '.')) {
                        [$relation, $relField] = explode('.', $col, 2);
                        $query->whereHas($relation, fn($q) => $q->where($relField, $val));
                    } elseif ($col === 'fish_breeds_id' && !is_numeric($val) && !\Illuminate\Support\Str::isUuid($val)) {
                        $query->whereHas('fishBreed', fn($q) => $q->where('name', $val));
                    } elseif ($col === 'lakes_id' && !is_numeric($val) && !\Illuminate\Support\Str::isUuid($val)) {
                        $query->whereHas('lake', fn($q) => $q->where('name', $val));
                    } elseif ($col === 'anglers_id' && !is_numeric($val) && !\Illuminate\Support\Str::isUuid($val)) {
                        $query->whereHas('angler', fn($q) => $q->where(DB::raw("CONCAT(firstName, ' ', lastName)"), 'like', '%' . $val . '%')->orWhere('lastName', $val));
                    } else {
                        $query->where($col, $val);
                    }
                }
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
                    if ($this->modelClass === Record::class) {
                        $searchableCols = ['fishBreed.name', 'lake.name', 'angler.firstName', 'angler.lastName', 'lure.name'];
                    } else {
                        $searchableCols = ['name', 'title', 'firstName', 'lastName', 'email', 'description'];
                    }
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
            } elseif ($sortColKey === 'lake' || $sortColKey === 'lake.name') {
                if ($this->modelClass === Record::class) {
                    $query->leftJoin('lakes', 'records.lakes_id', '=', 'lakes.id')
                          ->select('records.*')
                          ->orderBy('lakes.name', $order);
                    continue;
                }
            } elseif ($sortColKey === 'species' || $sortColKey === 'species_name' || $sortColKey === 'fishBreed.name') {
                if ($this->modelClass === Record::class) {
                    $query->leftJoin('fish_breeds', 'records.fish_breeds_id', '=', 'fish_breeds.id')
                          ->select('records.*')
                          ->orderBy('fish_breeds.name', $order);
                    continue;
                }
            } elseif ($sortColKey === 'dailyWeather' || $sortColKey === 'weather' || $sortColKey === 'air_temp') {
                if ($this->modelClass === Record::class) {
                    $query->leftJoin('lake_daily_weather', function ($join) {
                        $join->on('records.lakes_id', '=', 'lake_daily_weather.lakes_id')
                             ->on(DB::raw('DATE(records.caught)'), '=', 'lake_daily_weather.date');
                    })
                    ->select('records.*')
                    ->orderBy('lake_daily_weather.air_temp_mean', $order);
                    continue;
                }
            } elseif ($sortColKey === 'angler' || $sortColKey === 'angler.lastName') {
                if ($this->modelClass === Record::class) {
                    $query->leftJoin('anglers', 'records.anglers_id', '=', 'anglers.id')
                          ->select('records.*')
                          ->orderBy('anglers.lastName', $order)
                          ->orderBy('anglers.firstName', $order);
                    continue;
                } elseif ($this->modelClass === User::class) {
                    $query->leftJoin('anglers', 'users.id', '=', 'anglers.user_id')
                          ->select('users.*')
                          ->orderBy('anglers.lastName', $order)
                          ->orderBy('anglers.firstName', $order);
                    continue;
                }
            } elseif ($sortColKey === 'lure' || $sortColKey === 'lure.name') {
                if ($this->modelClass === Record::class) {
                    $query->leftJoin('lures', 'records.lures_id', '=', 'lures.id')
                          ->select('records.*')
                          ->orderBy('lures.name', $order);
                    continue;
                }
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
