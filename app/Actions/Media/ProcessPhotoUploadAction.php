<?php

namespace Fishinglog\Actions\Media;

use Fishinglog\Models\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProcessPhotoUploadAction
{
    /**
     * Store uploaded file and attach polymorphic Photo model to target entity.
     *
     * @param Model $target
     * @param UploadedFile $file
     * @param string $folder
     * @param bool $isCover
     * @return Photo
     */
    public function execute(Model $target, UploadedFile $file, string $folder = 'photos', bool $isCover = false): Photo
    {
        $path = $file->store($folder, 'public');

        return Photo::create([
            'photoable_type' => get_class($target),
            'photoable_id' => $target->id,
            'path' => $path,
            'caption' => $file->getClientOriginalName(),
            'is_cover' => $isCover,
            'sync_status' => 'pending_upstream',
        ]);
    }
}
