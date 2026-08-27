<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\PipeContract;
use Illuminate\Support\Facades\Schema;

class SortBy implements PipeContract
{
    /**
     * Standard column mapping aliases.
     */
    protected array $columnMaps = [
        'date' => 'caught',
        'species' => 'name',
        'status' => 'released',
    ];

    public function handle($query, Closure $next)
    {
        $sortBy = request('sort_by');
        $model = method_exists($query, 'getModel') ? $query->getModel() : null;
        $table = $model ? $model->getTable() : null;

        // Apply default table sorting if no explicit sort parameter is provided
        if (!$sortBy) {
            $query->reorder();

            if ($table === 'records') {
                $query->orderBy('records.caught', 'desc');
            } elseif ($table === 'anglers') {
                $query->orderBy('anglers.lastName', 'asc')
                      ->orderBy('anglers.firstName', 'asc')
                      ->orderBy('anglers.middleName', 'asc');
            } elseif ($table && Schema::hasColumn($table, 'name')) {
                $query->orderBy("{$table}.name", 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            return $next($query);
        }

        $sortCols = array_filter(explode(',', $sortBy));
        $sortOrders = explode(',', request('sort_order', 'asc'));

        if (empty($sortCols)) {
            return $next($query);
        }

        // Clear default orderBy clauses set prior to pipeline execution
        $query->reorder();
        $joinedTables = [];



        foreach ($sortCols as $index => $col) {
            $order = strtolower($sortOrders[$index] ?? 'asc') === 'desc' ? 'desc' : 'asc';

            if ($table === 'anglers') {
                switch ($col) {
                    case 'name':
                    case 'angler':
                    case 'lastName':
                    case 'firstName':
                        $query->orderBy('anglers.lastName', $order)
                              ->orderBy('anglers.firstName', $order)
                              ->orderBy('anglers.middleName', $order);
                        continue 2;

                    case 'catches':
                        $query->orderBy('records_count', $order);
                        continue 2;

                    case 'lakes':
                        $query->orderBy('lakes_count', $order);
                        continue 2;
                }
            }

            if ($table === 'lakes') {
                switch ($col) {
                    case 'records_count':
                    case 'catches':
                        $query->orderByRaw("`records_count` {$order}");
                        continue 2;

                    case 'visits':
                        $query->orderByRaw("`visits` {$order}");
                        continue 2;

                    case 'anglers_count':
                    case 'anglers':
                        $query->orderByRaw("`anglers_count` {$order}");
                        continue 2;

                    case 'rate':
                        $query->orderByRaw("(records_count / NULLIF(visits, 0)) {$order}");
                        continue 2;

                    case 'lat':
                    case 'latitude':
                        $query->orderBy('lakes.latitude', $order);
                        continue 2;

                    case 'long':
                    case 'longitude':
                        $query->orderBy('lakes.longitude', $order);
                        continue 2;
                }
            }

            if ($table === 'records') {

                switch ($col) {
                    case 'angler':
                        if (!in_array('anglers', $joinedTables)) {
                            $query->select('records.*')->join('anglers', 'records.anglers_id', '=', 'anglers.id');
                            $joinedTables[] = 'anglers';
                        }
                        $query->orderBy('anglers.lastName', $order)
                              ->orderBy('anglers.firstName', $order)
                              ->orderBy('anglers.middleName', $order);
                        continue 2;


                    case 'lake':
                        if (!in_array('lakes', $joinedTables)) {
                            $query->select('records.*')->join('lakes', 'records.lakes_id', '=', 'lakes.id');
                            $joinedTables[] = 'lakes';
                        }
                        $query->orderBy('lakes.name', $order);
                        continue 2;

                    case 'species':
                        if (!in_array('fish_breeds', $joinedTables)) {
                            $query->select('records.*')->join('fish_breeds', 'records.fish_breeds_id', '=', 'fish_breeds.id');
                            $joinedTables[] = 'fish_breeds';
                        }
                        $query->orderBy('fish_breeds.name', $order);
                        continue 2;

                    case 'lure':
                        if (!in_array('lures', $joinedTables)) {
                            $query->select('records.*')->leftJoin('lures', 'records.lures_id', '=', 'lures.id');
                            $joinedTables[] = 'lures';
                        }
                        $query->orderBy('lures.name', $order);
                        continue 2;

                    case 'date':
                        $query->orderBy('records.caught', $order);
                        continue 2;

                    case 'weight':
                        $query->orderBy('records.weight', $order);
                        continue 2;

                    case 'length':
                        $query->orderBy('records.length', $order);
                        continue 2;

                    case 'status':
                        $query->orderBy('records.released', $order);
                        continue 2;
                }
            }

            // Generic handling for other tables
            $targetColumn = $this->columnMaps[$col] ?? $col;

            if ($table && !str_contains($targetColumn, '.')) {
                $targetColumn = "{$table}.{$targetColumn}";
            }

            $query->orderBy($targetColumn, $order);
        }

        return $next($query);
    }
}
