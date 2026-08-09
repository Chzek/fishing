<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\FilterPipeContract;

class FilterByAngler implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        $anglerParam = request('angler') ?: request('angler_id');

        if ($anglerParam) {
            if (is_numeric($anglerParam)) {
                $query->where('anglers_id', $anglerParam);
            } else {
                $query->whereHas('angler', function ($a) use ($anglerParam) {
                    $a->where('firstName', 'like', "%{$anglerParam}%")
                      ->orWhere('lastName', 'like', "%{$anglerParam}%")
                      ->orWhereRaw("CONCAT(COALESCE(firstName, ''), ' ', COALESCE(lastName, '')) LIKE ?", ["%{$anglerParam}%"]);
                });
            }
        }
        
        return $next($query);
    }
}