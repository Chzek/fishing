<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\FilterPipeContract;

class FilterByLure implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        $lureId = request('lures_id') ?: request('lure_id');

        if ($lureId) {
            $query->where('lures_id', $lureId);
        }

        return $next($query);
    }
}
