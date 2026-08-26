<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\FilterPipeContract;

class FilterBySpecies implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        $species = request('species') ?: request('species_id');

        if ($species) {
            $query->where(function ($q) use ($species) {
                $q->where('fish_breeds_id', $species)
                  ->orWhereHas('fishBreed', function ($b) use ($species) {
                      $b->where('name', $species);
                  });
            });
        }

        return $next($query);
    }
}
