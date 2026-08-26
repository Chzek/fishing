<?php

namespace Fishinglog\Actions\Records;

use Fishinglog\Models\Record;
use Illuminate\Support\Facades\Cache;

class CreateCatchRecordAction
{
    /**
     * Execute the action to create a new catch record.
     *
     * @param array $data
     * @return Record
     */
    public function execute(array $data): Record
    {
        $record = Record::create($data);

        // Clear cached aggregate overview statistics
        Cache::forget('angler_stats_overview');

        return $record;
    }
}
