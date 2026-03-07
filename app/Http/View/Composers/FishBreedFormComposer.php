<?php

namespace Fishinglog\Http\View\Composers;

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
        $tempFamilies = \Fishinglog\FishFamily::all();
        $breeds = \Fishinglog\FishBreed::all();

        $families = [];
        foreach($tempFamilies as $family) {
            $families[$family->id] = $family->name;
        }

        $view->with([
            'families' => $families,
            'breeds' => $breeds,
        ]);
    }
}
