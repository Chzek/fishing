<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\FilterPipeContract;

class FilterByLake implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        $lake = request('lake') ?: request('lake_id');

        if ($lake) {
            $query->where(function ($q) use ($lake) {
                $q->where('lakes_id', $lake)
                  ->orWhereHas('lake', function ($l) use ($lake) {
                      $l->where('name', $lake);
                  });
            });
        }

        return $next($query);
    }
}
