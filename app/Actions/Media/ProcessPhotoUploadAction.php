<?php

namespace Fishinglog\Actions\Media;

use Fishinglog\Models\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProcessPhotoUploadAction
{
    /**
     * Store uploaded file and attach polymorphic Photo model to target entity.
     *
     * @param Model $target
     * @param UploadedFile $file
     * @param string $folder
     * @param bool $isCover
     * @param string|null $caption
     * @param int|string|null $userId
     * @return Photo
     */
    public function execute(
        Model $target,
        UploadedFile $file,
        string $folder = 'photos',
        bool $isCover = false,
        ?string $caption = null,
        int|string|null $userId = null
    ): Photo {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::uuid() . '.' . $extension;
        $path = $file->storeAs($folder, $filename, 'public');

        return Photo::create([
            'photoable_type' => get_class($target),
            'photoable_id' => (string) $target->getKey(),
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'caption' => $caption ?? $file->getClientOriginalName(),
            'is_cover' => $isCover,
            'user_id' => $userId ?? auth()->id(),
            'sync_status' => 'pending_upstream',
        ]);
    }
}
