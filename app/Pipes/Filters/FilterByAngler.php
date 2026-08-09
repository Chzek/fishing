<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Models\Angler;
use Fishinglog\Pipes\FilterPipeContract;
use Illuminate\Support\Str;

class FilterByAngler implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        $anglerParam = request('angler') ?: request('angler_id');

        if ($anglerParam) {
            if (Str::isUuid($anglerParam) || is_numeric($anglerParam) || Angler::where('id', $anglerParam)->exists()) {
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