<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\PipeContract;

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

        if (!$sortBy) {
            return $next($query);
        }

        $sortOrder = strtolower(request('sort_order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $model = method_exists($query, 'getModel') ? $query->getModel() : null;
        $table = $model ? $model->getTable() : null;

        // Specialized sorting for Records (Catches) table
        if ($table === 'records') {
            switch ($sortBy) {
                case 'angler':
                    $query->select('records.*')
                        ->join('anglers', 'records.anglers_id', '=', 'anglers.id')
                        ->orderBy('anglers.firstName', $sortOrder)
                        ->orderBy('anglers.lastName', $sortOrder);
                    return $next($query);

                case 'lake':
                    $query->select('records.*')
                        ->join('lakes', 'records.lakes_id', '=', 'lakes.id')
                        ->orderBy('lakes.name', $sortOrder);
                    return $next($query);

                case 'species':
                    $query->select('records.*')
                        ->join('fish_breeds', 'records.fish_breeds_id', '=', 'fish_breeds.id')
                        ->orderBy('fish_breeds.name', $sortOrder);
                    return $next($query);

                case 'lure':
                    $query->select('records.*')
                        ->leftJoin('lures', 'records.lures_id', '=', 'lures.id')
                        ->orderBy('lures.name', $sortOrder);
                    return $next($query);

                case 'date':
                    $query->orderBy('records.caught', $sortOrder);
                    return $next($query);

                case 'weight':
                    $query->orderBy('records.weight', $sortOrder);
                    return $next($query);

                case 'length':
                    $query->orderBy('records.length', $sortOrder);
                    return $next($query);

                case 'status':
                    $query->orderBy('records.released', $sortOrder);
                    return $next($query);
            }
        }

        // Generic handling for other tables
        $targetColumn = $this->columnMaps[$sortBy] ?? $sortBy;

        if ($table && !str_contains($targetColumn, '.')) {
            $targetColumn = "{$table}.{$targetColumn}";
        }

        $query->orderBy($targetColumn, $sortOrder);

        return $next($query);
    }
}