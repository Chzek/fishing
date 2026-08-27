<?php

namespace Fishinglog\Http\Controllers\Auth;

use Fishinglog\Http\Controllers\Controller;

class VerificationController extends Controller
{
    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected string $redirectTo = '/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}
