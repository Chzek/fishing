<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Fishinglog\Services\AnglerTelemetryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param AnglerTelemetryService $telemetryService
     * @return \Illuminate\View\View
     */
    public function show(AnglerTelemetryService $telemetryService)
    {
        $angler = Angler::where('user_id', Auth::id())
            ->with(['earnedBadges.badge'])
            ->first();

        $telemetry = $telemetryService->getAnglerTelemetry($angler);
        $unreadNotifications = Auth::user() ? Auth::user()->unreadNotifications : collect();

        return view('profile.show', array_merge($telemetry, [
            'unreadNotifications' => $unreadNotifications,
        ]));
    }

    /**
     * Edit the authenticated user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the authenticated user's profile and password.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'theme_preference' => 'nullable|string|in:system,light,dark',
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match your account password.']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('theme_preference')) {
            $user->theme_preference = $request->theme_preference;
        }
        $user->save();

        return redirect('/profile/edit')->with('status', 'Account preferences and password updated successfully.');
    }
}
