<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\PipeContract;

class SortBy implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $sortBy = request('sort_by', 'name');
        $sortOrder = request('sort_order', 'asc');

        if(!request('sort_by'))
            return $next($query);
        
        $query->orderBy($sortBy, $sortOrder);

        return $next($query);
    }
}