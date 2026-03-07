<?php

namespace Fishinglog\Livewire;

use Carbon\Carbon;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;

class ComposerOutdated extends Card
{
    public function render()
    {
        $packages = Pulse::values('composer_outdated', ['result'])->first();

        try{
            $lastUpdated = Carbon::createFromTimestamp($packages->timestamp);
        }catch(\Exception $e) {
            $lastUpdated = Carbon::parse("2000-01-01");
        }

        $packages = $packages
            ? json_decode($packages->value, JSON_THROW_ON_ERROR)['installed']
            : [];

        return view('livewire.composer-outdated', [
            'packages' => $packages,
            'lastUpdated' => $lastUpdated->toDateString(),
        ]);
    }
}
