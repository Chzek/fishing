<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\Photo;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    /**
     * Store batch uploaded photos for a polymorphic entity (Expedition or Record).
     */
    public function store(Request $request)
    {
        $request->validate([
            'photoable_type' => 'required|string|in:record,expedition,angler',
            'photoable_id' => 'required|string',
            'photos' => 'required|array|min:1',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:20480',
            'caption' => 'nullable|string|max:500',
        ]);

        $typeMap = [
            'record' => Record::class,
            'expedition' => Expedition::class,
            'angler' => Angler::class,
        ];

        $modelClass = $typeMap[$request->photoable_type];
        $entity = $modelClass::findOrFail($request->photoable_id);

        $savedPhotos = [];
        $hasExistingPhotos = $entity->photos()->count() > 0;

        foreach ($request->file('photos') as $index => $file) {
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = Str::uuid() . '.' . $extension;
            $folder = 'photos/' . Str::plural($request->photoable_type);
            $path = $file->storeAs($folder, $filename, 'public');

            $photo = Photo::create([
                'photoable_type' => $modelClass,
                'photoable_id' => $entity->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'caption' => $request->caption,
                'is_cover' => (!$hasExistingPhotos && $index === 0),
                'user_id' => auth()->id(),
                'sync_status' => 'pending_upstream',
            ]);

            $savedPhotos[] = $photo;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => count($savedPhotos) . ' photo(s) uploaded successfully.',
                'photos' => $savedPhotos,
            ]);
        }

        return back()->with('success', count($savedPhotos) . ' photo(s) uploaded successfully.');
    }

    /**
     * Delete a photo.
     */
    public function destroy(Photo $photo)
    {
        $photo->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Photo removed.']);
        }

        return back()->with('success', 'Photo removed.');
    }

    /**
     * Set a photo as the cover photo for its parent entity.
     */
    public function setCover(Photo $photo)
    {
        Photo::where('photoable_type', $photo->photoable_type)
            ->where('photoable_id', $photo->photoable_id)
            ->update(['is_cover' => false]);

        $photo->is_cover = true;
        $photo->save();

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Cover photo updated.']);
        }

        return back()->with('success', 'Cover photo updated.');
    }

    /**
     * Set a photo as the profile avatar for the logged-in user's angler profile.
     */
    public function setAsAvatar(Photo $photo)
    {
        $user = auth()->user();
        if (!$user || !$user->angler) {
            return back()->with('error', 'You must have an associated Angler profile to set an avatar.');
        }

        $user->angler->setAvatarFromPhoto($photo);

        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Profile avatar updated!']);
        }

        return back()->with('success', 'Profile avatar updated from photo!');
    }
}
