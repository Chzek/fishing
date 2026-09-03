<?php

namespace Fishinglog\Http\Controllers\Admin;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminQuickAddController extends Controller
{
    /**
     * Admin offline quick-add user and angler creation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $nameParts = explode(' ', trim($request->name), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        // 1. Create User locally with pending_upstream status
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->markEmailAsVerified();

        // 2. Create associated Angler profile
        $angler = Angler::create([
            'firstName' => $firstName,
            'middleName' => '',
            'lastName' => $lastName,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.users')->with('status', "Offline Quick-Add successful! Angler account created for {$user->name} ({$user->email}). Pending NAS sync.");
    }
}
