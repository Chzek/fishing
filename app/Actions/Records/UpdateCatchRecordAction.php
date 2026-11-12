<?php

namespace Fishinglog\Actions\Records;

use Fishinglog\Actions\Media\ProcessPhotoUploadAction;
use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Models\Record;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateCatchRecordAction
{
    public function __construct(
        public ProcessPhotoUploadAction $photoUploadAction
    ) {
    }

    /**
     * Execute the action to update an existing catch record and process optional uploaded photos.
     *
     * @param Record $record
     * @param array<string, mixed> $data
     * @param array<int, UploadedFile> $uploadedPhotos
     * @param array<string, mixed> $context
     * @return Record
     */
    public function execute(
        Record $record,
        array $data,
        array $uploadedPhotos = [],
        array $context = []
    ): Record {
        return DB::transaction(function () use ($record, $data, $uploadedPhotos, $context): Record {
            $attributes = $data;
            unset($attributes['id'], $attributes['photos']);

            $record->fill($attributes);
            $record->save();

            if (!empty($uploadedPhotos)) {
                $hasExistingPhotos = $record->photos()->count() > 0;
                foreach ($uploadedPhotos as $index => $file) {
                    $this->photoUploadAction->execute(
                        target: $record,
                        file: $file,
                        folder: 'photos/records',
                        isCover: (!$hasExistingPhotos && $index === 0)
                    );
                }
            }

            if ($record->wasChanged(['anglers_id', 'lakes_id', 'fish_breeds_id', 'length', 'weight', 'released', 'caught', 'temperature'])) {
                CatchLoggedEvent::dispatch($record, array_merge(['updated' => true], $context));
            }

            return $record;
        });
    }
}

