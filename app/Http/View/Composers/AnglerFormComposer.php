<?php

namespace Fishinglog\Http\View\Composers;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\View\View;

class AnglerFormComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $unassigned = Angler::select('id')->get();
        $users = User::whereIn('id', $unassigned->toArray())->pluck('name', 'id');

        $view->with([
            'users' => $users,
        ]);
    }
}
