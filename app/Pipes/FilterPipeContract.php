<?php

namespace Fishinglog\Pipes;

use Closure;
use Illuminate\Database\Query\Builder;

interface FilterPipeContract
{
    public function handle(Builder $query, Closure $next);

}
