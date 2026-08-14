<?php

namespace Fishinglog\Http\Controllers\Admin;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class AdminInviteController extends Controller
{
    /**
     * Send email invitation with signed registration URL.
     */
    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
        ]);

        $relativeSignedPath = URL::temporarySignedRoute(
            'register.invited',
            now()->addDays(7),
            ['email' => $request->email, 'name' => $request->name],
            false
        );

        $localSignedUrl = url($relativeSignedPath);
        $nasBaseUrl = rtrim(env('NAS_URL', config('app.url')), '/');
        $nasSignedUrl = $nasBaseUrl . $relativeSignedPath;

        $preferredSignedUrl = (!empty(env('NAS_URL')) && env('NAS_URL') !== url('/')) ? $nasSignedUrl : $localSignedUrl;

        return redirect()->route('admin.users')->with([
            'status' => "Invitation signed URL generated for {$request->email}: {$preferredSignedUrl}",
            'invite_email' => $request->email,
            'invite_name' => $request->name,
            'invite_url_local' => $localSignedUrl,
            'invite_url_nas' => $nasSignedUrl,
            'invite_url' => $preferredSignedUrl,
        ]);
    }

    /**
     * Show registration form for invited user with valid signed URL.
     */
    public function showInvitedRegistration(Request $request)
    {
        if (!$request->hasValidRelativeSignature()) {
            abort(403, 'Invalid or expired invitation link.');
        }

        return view('auth.invited-register', [
            'email' => $request->query('email'),
            'name' => $request->query('name'),
        ]);
    }

    /**
     * Store registration for invited user.
     */
    public function processInvitedRegistration(Request $request)
    {
        if (!$request->hasValidRelativeSignature()) {
            abort(403, 'Invalid or expired invitation link.');
        }

        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->markEmailAsVerified();

        // Create associated Angler profile
        Angler::create([
            'firstName' => $user->name,
            'middleName' => '',
            'lastName' => 'Angler',
            'user_id' => $user->id,
        ]);

        auth()->login($user);

        return redirect()->route('home')->with('status', 'Welcome! Your account has been registered and verified.');
    }
}
