<?php

namespace Fishinglog\Http\View\Composers;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Illuminate\View\View;

class FishBreedFormComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $tempFamilies = FishFamily::all();
        $breeds = FishBreed::all();

        $families = [];
        foreach ($tempFamilies as $family) {
            $families[$family->id] = $family->name;
        }

        $view->with([
            'families' => $families,
            'breeds' => $breeds,
        ]);
    }
}
