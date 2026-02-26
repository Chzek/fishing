<?php

namespace Fishinglog\Http\View\Composers;

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
        // For simplicity and to cover both create/edit cases broadly, we'll
        // fetch all unassigned anglers (plus currently assigned ones if needed, but the original code just did this).
        // Since we are moving this to a composer, we'll use the edit logic `Angler::select('id')->get()` 
        // which includes all angler ids, allowing any corresponding users to be selected.
        $unassigned = \Fishinglog\Angler::select('id')->get();

        $users = \Fishinglog\User::whereIn('id', $unassigned->toArray())->pluck('name', 'id');

        $view->with([
            'users' => $users,
        ]);
    }
}
