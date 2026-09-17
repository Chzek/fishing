<?php

namespace Fishinglog\Events;

use Fishinglog\Models\Record;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CatchLoggedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Record $record
     * @param array<string, mixed> $context
     */
    public function __construct(
        public Record $record,
        public array $context = []
    ) {}
}
