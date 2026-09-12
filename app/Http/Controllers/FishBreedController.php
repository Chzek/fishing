<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Actions\Media\ProcessPhotoUploadAction;
use Fishinglog\Http\Requests\StoreFishBreedRequest;
use Fishinglog\Http\Requests\UpdateFishBreedRequest;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Illuminate\Http\Request;

class FishBreedController extends Controller
{
    public function __construct(
        protected ProcessPhotoUploadAction $photoUploadAction
    ) {}

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
            $this->photoUploadAction->optimizeAndSave($request->avatar, 'fish/avatars/' . $avatarName, 600);
            $breed->avatar = $avatarName;
        }

        if ($request->hasFile('image')) {
            $imageName = 'fish_img_' . time() . '.' . $request->image->getClientOriginalExtension();
            $this->photoUploadAction->optimizeAndSave($request->image, 'fish/' . $imageName, 1600, syncToLegacyPublic: true);
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
            $this->photoUploadAction->optimizeAndSave($request->avatar, 'fish/avatars/' . $avatarName, 600);
            $breed->avatar = $avatarName;
        }

        if ($request->hasFile('image')) {
            $imageName = 'fish_img_' . time() . '.' . $request->image->getClientOriginalExtension();
            $this->photoUploadAction->optimizeAndSave($request->image, 'fish/' . $imageName, 1600, syncToLegacyPublic: true);
            $breed->image = $imageName;
        }

        $breed->save();

        return redirect('/fish/' . $breed->id);
    }
}
