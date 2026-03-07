<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\FilterPipeContract;

class FilterByWeight implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        if(request('weight') && request('weight_operator')) {
            $query->where('weight', request('weight_operator', "="), request('weight'));
        }
        
        return $next($query);
    }
}