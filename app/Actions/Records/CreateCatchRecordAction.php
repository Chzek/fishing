<?php

namespace Fishinglog\Actions\Records;

use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Models\Record;

class CreateCatchRecordAction
{
    /**
     * Execute the action to create a new catch record.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $context
     * @return Record
     */
    public function execute(array $data, array $context = []): Record
    {
        $record = Record::create($data);

        CatchLoggedEvent::dispatch($record, $context);

        return $record;
    }
}
