<?php

namespace Fishinglog\Http\View\Composers;

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
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        $view->with([
            'users' => $users,
        ]);
    }
}

