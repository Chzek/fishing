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
                      ->orWhere('lastName', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT(COALESCE(firstName, ''), ' ', COALESCE(lastName, '')) LIKE ?", ["%{$search}%"]);

                    $words = array_filter(explode(' ', trim($search)));
                    if (count($words) > 1) {
                        $a->orWhere(function ($sub) use ($words) {
                            foreach ($words as $word) {
                                $sub->where(function ($w) use ($word) {
                                    $w->where('firstName', 'like', "%{$word}%")
                                      ->orWhere('lastName', 'like', "%{$word}%");
                                });
                            }
                        });
                    }
                })
                ->orWhereHas('lure', function ($lu) use ($search) {
                    $lu->where('name', 'like', "%{$search}%");
                });
            });
        }

        return $next($query);
    }
}
