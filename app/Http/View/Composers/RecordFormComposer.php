<?php

namespace Fishinglog\Http\View\Composers;

use Illuminate\View\View;

class RecordFormComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $temp = \Fishinglog\Angler::orderBy('lastName', 'asc')
            ->orderBy('firstName', 'asc')
            ->orderBy('middleName', 'asc')
            ->get();

        $anglers[null] = "Select an Angler";
        foreach($temp as $angler) {
            $anglers[$angler->id] = $angler->fullName;
        }

        $temp = \Fishinglog\Lake::orderBy('name', 'asc')->get();
        $lakes[null] = "Select a Lake";
        foreach($temp as $lake) {
            $lakes[$lake->id] = $lake->name;
        }

        $temp = \Fishinglog\FishBreed::orderBy('name', 'asc')->get();
        $fishes[null] = "Select a Fish";
        foreach($temp as $fish) {
            $fishes[$fish->id] = $fish->name;
        }

        $temp = \Fishinglog\Lure::orderBy('name', 'asc')
            ->orderBy('color', 'asc')
            ->orderBy('size', 'desc')
            ->get();

        $lures[null] = "Select a Lure";
        foreach($temp as $lure) {
            $lures[$lure->id] = $lure->displayName;
        }

        $view->with([
            'anglers' => $anglers,
            'lakes' => $lakes,
            'fishes' => $fishes,
            'lures' => $lures,
        ]);
    }
}
