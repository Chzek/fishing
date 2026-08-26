<?php

namespace Fishinglog\Actions\Lures;

use Fishinglog\Models\Lure;
use Illuminate\Support\Collection;

class CreateLureVariantAction
{
    /**
     * Execute the action to create one or more lure color variants.
     *
     * @param array $attributes
     * @param string|null $colorsInput
     * @return Collection<int, Lure>
     */
    public function execute(array $attributes, ?string $colorsInput = null): Collection
    {
        $created = collect();

        if (!empty($colorsInput)) {
            $rawColors = explode(',', $colorsInput);
            foreach ($rawColors as $rawColor) {
                $color = trim($rawColor);
                if (!empty($color)) {
                    $lure = Lure::firstOrCreate(
                        [
                            'name' => $attributes['name'],
                            'color' => $color,
                            'size' => $attributes['size'] ?? null,
                        ],
                        [
                            'brand' => $attributes['brand'] ?? null,
                            'category' => $attributes['category'] ?? 'Other',
                            'weight' => $attributes['weight'] ?? ($attributes['size'] ?? null),
                            'depth_range' => $attributes['depth_range'] ?? null,
                        ]
                    );
                    $created->push($lure);
                }
            }
        } else {
            $lure = Lure::create([
                'name' => $attributes['name'],
                'brand' => $attributes['brand'] ?? null,
                'category' => $attributes['category'] ?? 'Other',
                'color' => $attributes['color'] ?? null,
                'size' => $attributes['size'] ?? null,
                'weight' => $attributes['weight'] ?? ($attributes['size'] ?? null),
                'depth_range' => $attributes['depth_range'] ?? null,
            ]);
            $created->push($lure);
        }

        return $created;
    }
}
