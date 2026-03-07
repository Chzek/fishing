<?php

namespace Fishinglog\Http\View\Composers;

use Illuminate\View\View;

class CrewFormComposer
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

        $temp = \Fishinglog\Expedition::all();
        foreach($temp as $expedition) {
            $expeditions[$expedition->id] = $expedition->description;
        }

        $view->with([
            'anglers' => $anglers,
            'expeditions' => $expeditions,
        ]);
    }
}
