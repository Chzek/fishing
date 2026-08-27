<?php

namespace Fishinglog\Http\Controllers\Angler;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Http\Requests\StoreAnglerRequest;
use Fishinglog\Http\Requests\UpdateAnglerAvatarRequest;
use Fishinglog\Http\Requests\UpdateAnglerRequest;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Crew;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnglerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('angler.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $angler = new Angler;
        $users = \Fishinglog\Models\User::select('id', 'name', 'email')->orderBy('name')->get();

        return view('angler.create', [
            'angler' => $angler,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\StoreAnglerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAnglerRequest $request)
    {
        $angler = new Angler;
        $angler->firstName = $request->firstName;
        $angler->middleName = $request->middleName;
        $angler->lastName = $request->lastName;
        $angler->user_id = $request->user_id;
        $angler->birthdate = $request->birthdate;

        $angler->save();

        return redirect('/angler');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Models\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function show(Angler $angler)
    {
        $records = Record::where('anglers_id', $angler->id)
            ->orderBy('caught', 'desc')
            ->with('fishBreed')
            ->take(10)
            ->get();

        $longest = Record::where('anglers_id', $angler->id)
            ->orderBy('length', 'desc')
            ->first();

        $count = Record::where('anglers_id', $angler->id)->count();

        $crews = Crew::where('anglers_id', $angler->id)->count();

        return view('angler.show', [
            'angler' => $angler,
            'records' => $records,
            'longest' => $longest,
            'count' => $count,
            'crews' => $crews,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Models\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function edit(Angler $angler)
    {
        $users = \Fishinglog\Models\User::select('id', 'name', 'email')->orderBy('name')->get();

        return view('angler.edit', [
            'angler' => $angler,
            'users' => $users,
        ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\UpdateAnglerRequest  $request
     * @param  \Fishinglog\Models\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAnglerRequest $request, Angler $angler)
    {
        $targetAngler = Angler::find($request->id) ?? $angler;

        $targetAngler->firstName = $request->firstName;
        $targetAngler->middleName = $request->middleName;
        $targetAngler->lastName = $request->lastName;
        $targetAngler->user_id = $request->user_id;
        $targetAngler->birthdate = $request->birthdate;

        if ($request->hasFile('avatar')) {
            $avatarName = 'avatar_' . time() . '.' . $request->avatar->getClientOriginalExtension();
            $this->optimizeAndSaveImage($request->avatar, 'avatars/' . $avatarName, 600);
            $targetAngler->avatar = $avatarName;
        }

        $targetAngler->save();

        return redirect('/angler/' . $targetAngler->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Models\Angler  $angler
     * @return \Illuminate\Http\Response
     */
    public function destroy(Angler $angler)
    {
        $angler->delete();

        return redirect('/angler')->with('status', 'Angler profile removed successfully.');
    }

    public function updateAvatar(UpdateAnglerAvatarRequest $request)
    {
        $angler = Auth::user()->angler;

        $avatarName = 'avatar_' . $angler->id . '_' . time() . '.' . $request->avatar->getClientOriginalExtension();
        $this->optimizeAndSaveImage($request->avatar, 'avatars/' . $avatarName, 600);

        $angler->avatar = $avatarName;
        $angler->save();

        return back()->with('success', 'You have successfully uploaded your avatar.');
    }

    /**
     * Compress and resize an uploaded image to a maximum dimension and quality.
     */
    private function optimizeAndSaveImage($file, string $relativeStoragePath, int $maxDimension = 600): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']) && extension_loaded('gd')) {
            $srcImage = match ($extension) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($file->getRealPath()),
                'png' => @imagecreatefrompng($file->getRealPath()),
                'webp' => @imagecreatefromwebp($file->getRealPath()),
                default => null,
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
                    'webp' => imagewebp($dstImage, null, 85),
                };
                $compressedData = ob_get_clean();

                imagedestroy($srcImage);
                imagedestroy($dstImage);

                if ($compressedData) {
                    $disk->put($relativeStoragePath, $compressedData);
                    return;
                }
            }
        }

        // Fallback standard storage
        $file->storeAs(dirname($relativeStoragePath), basename($relativeStoragePath), 'public');
    }

}

