<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\FilterPipeContract;

class FilterByLength implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        if(request('length') && request('length_operator')) {
            $query->having('length', request('length_operator', "="), request('length'));
        }
        
        return $next($query);
    }
}