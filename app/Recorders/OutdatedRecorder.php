<?php

namespace Fishinglog\Recorders;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Laravel\Pulse\Events\SharedBeat;
use Laravel\Pulse\Pulse;
use RuntimeException;

class OutdatedRecorder
{
    public string $listen = SharedBeat::class;

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config
    )
    {
        //
    }

    public function record(SharedBeat $event): void
    {
        if( $event->time->hour % 8 !== 0 ) { //$event->time->startOfDay()) {
            return;
        }

        $result = Process::run("composer outdated -D -f json");

        if($result->failed()) {
            throw new RuntimeException(
                'Composer outdated failed: ' . $result->errorOutput()
            );
        }

        json_decode($result->output(), true, 512, JSON_THROW_ON_ERROR);

        $this->pulse->set('composer_outdated', 'result', $result->output());
    }
}

