<?php

namespace Fishinglog\Pipes\Filters;

use Closure;
use Fishinglog\Pipes\FilterPipeContract;

class FilterBySearch implements FilterPipeContract
{
    public function handle($query, Closure $next)
    {
        $search = request('search') ?: request('q');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('fishBreed', function ($b) use ($search) {
                    $b->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('lake', function ($l) use ($search) {
                    $l->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('angler', function ($a) use ($search) {
                    $a->where('firstName', 'like', "%{$search}%")
                      ->orWhere('lastName', 'like', "%{$search}%");
                })
                ->orWhereHas('lure', function ($lu) use ($search) {
                    $lu->where('name', 'like', "%{$search}%");
                });
            });
        }

        return $next($query);
    }
}
