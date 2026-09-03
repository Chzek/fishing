<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Http\Requests\StoreFishBreedRequest;
use Fishinglog\Http\Requests\UpdateFishBreedRequest;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Illuminate\Http\Request;

class FishBreedController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $breed = new FishBreed;

        return view('fish.breed.create', [
            'breed' => $breed,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\StoreFishBreedRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreFishBreedRequest $request)
    {
        $breed = new FishBreed;
        $breed->name = $request->name;
        $breed->fish_families_id = $request->fish_families_id;

        if ($request->hasFile('avatar')) {
            $avatarName = 'fish_avatar_' . time() . '.' . $request->avatar->getClientOriginalExtension();
            $this->optimizeAndSaveImage($request->avatar, 'fish/avatars/' . $avatarName, 600);
            $breed->avatar = $avatarName;
        }

        if ($request->hasFile('image')) {
            $imageName = 'fish_img_' . time() . '.' . $request->image->getClientOriginalExtension();
            $this->optimizeAndSaveImage($request->image, 'fish/' . $imageName, 1600);
            $breed->image = $imageName;
        }

        $breed->save();

        return redirect('/fish');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Models\FishBreed  $fishBreed
     * @return \Illuminate\View\View
     */
    public function edit(FishBreed $fishBreed)
    {
        return view('fish.breed.edit', [
            'breed' => $fishBreed,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\UpdateFishBreedRequest  $request
     * @param  \Fishinglog\Models\FishBreed  $fishBreed
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateFishBreedRequest $request, FishBreed $fishBreed)
    {
        $breed = FishBreed::find($request->id) ?? $fishBreed;

        $breed->fish_families_id = $request->fish_families_id;
        $breed->name = $request->name;

        if ($request->hasFile('avatar')) {
            $avatarName = 'fish_avatar_' . time() . '.' . $request->avatar->getClientOriginalExtension();
            $this->optimizeAndSaveImage($request->avatar, 'fish/avatars/' . $avatarName, 600);
            $breed->avatar = $avatarName;
        }

        if ($request->hasFile('image')) {
            $imageName = 'fish_img_' . time() . '.' . $request->image->getClientOriginalExtension();
            $this->optimizeAndSaveImage($request->image, 'fish/' . $imageName, 1600);
            $breed->image = $imageName;
        }

        $breed->save();

        return redirect('/fish/' . $breed->id);
    }

    /**
     * Compress and resize an uploaded image to a maximum dimension and quality.
     */
    private function optimizeAndSaveImage($file, string $relativeStoragePath, int $maxDimension = 1600): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $disk = \Illuminate\Support\Facades\Storage::disk('public');

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

                    // Copy to public directory for legacy asset paths if applicable
                    $publicPath = public_path('images/' . ltrim(str_replace('fish/', '', $relativeStoragePath), '/'));
                    $publicDir = dirname($publicPath);
                    if (!file_exists($publicDir)) {
                        @mkdir($publicDir, 0755, true);
                    }
                    @file_put_contents($publicPath, $compressedData);
                    return;
                }
            }
        }

        // Fallback standard storage
        $file->storeAs(dirname($relativeStoragePath), basename($relativeStoragePath), 'public');
    }
}
