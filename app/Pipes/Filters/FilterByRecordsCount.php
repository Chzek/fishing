<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\FilterPipeContract;

class FilterByRecordsCount implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        if(request('records_count') && request('records_count_operator')) {
            $query->having('records_count', request('records_count_operator', "="), request('records_count'));
        }
        
        return $next($query);
    }
}