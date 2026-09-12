<?php

namespace Fishinglog\Actions\Media;

use Fishinglog\Models\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    /**
     * Compress, resize, and save an uploaded image to disk.
     *
     * @param UploadedFile $file
     * @param string $relativeStoragePath
     * @param int $maxDimension
     * @param bool $syncToLegacyPublic
     * @return void
     */
    public function optimizeAndSave(
        UploadedFile $file,
        string $relativeStoragePath,
        int $maxDimension = 1600,
        bool $syncToLegacyPublic = false
    ): void {
        $extension = strtolower($file->getClientOriginalExtension());
        $disk = Storage::disk('public');

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']) && extension_loaded('gd')) {
            $srcImage = match ($extension) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($file->getRealPath()),
                'png' => @imagecreatefrompng($file->getRealPath()),
                default => @imagecreatefromwebp($file->getRealPath()),
            };

            if ($srcImage) {
                $origWidth = imagesx($srcImage);
                $origHeight = imagesy($srcImage);

                if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
                    if ($origWidth >= $origHeight) {
                        $newWidth = $maxDimension;
                        $newHeight = (int) round(($origHeight / $origWidth) * $maxDimension);
                    } else {
                        $newHeight = $maxDimension;
                        $newWidth = (int) round(($origWidth / $origHeight) * $maxDimension);
                    }
                } else {
                    $newWidth = $origWidth;
                    $newHeight = $origHeight;
                }

                $dstImage = imagecreatetruecolor($newWidth, $newHeight);

                if (in_array($extension, ['png', 'webp'])) {
                    imagealphablending($dstImage, false);
                    imagesavealpha($dstImage, true);
                }

                imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

                ob_start();
                match ($extension) {
                    'jpg', 'jpeg' => imagejpeg($dstImage, null, 85),
                    'png' => imagepng($dstImage, null, 8),
                    default => imagewebp($dstImage, null, 85),
                };
                $compressedData = ob_get_clean();

                imagedestroy($srcImage);
                imagedestroy($dstImage);

                if ($compressedData) {
                    $disk->put($relativeStoragePath, $compressedData);

                    if ($syncToLegacyPublic) {
                        $publicPath = public_path('images/' . ltrim(str_replace('fish/', '', $relativeStoragePath), '/'));
                        $publicDir = dirname($publicPath);
                        if (!file_exists($publicDir)) {
                            @mkdir($publicDir, 0755, true);
                        }
                        @file_put_contents($publicPath, $compressedData);
                    }
                    return;
                }
            }
        }

        // Fallback standard storage
        $file->storeAs(dirname($relativeStoragePath), basename($relativeStoragePath), 'public');
    }
}
