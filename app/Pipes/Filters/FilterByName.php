<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\FilterPipeContract;

class FilterByName implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        if(request('name')) {
            $query->where('name', 'like', "%".request('name')."%");
        }
        
        return $next($query);
    }
}