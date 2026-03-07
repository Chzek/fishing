<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\FilterPipeContract;

class FilterByAngler implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        if(request('angler')) {
            $query->where('anglers.name', 'like', "%".request('angler')."%");
        }
        
        return $next($query);
    }
}