<?php

namespace App\Http\Controllers\Auth;

use App\Models\Code;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\LoginToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\CodeRequest;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\LoginNotification;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
        ]);
    }

     /**
     * Display the QR Code login view.
     */
    public function create_code(): Response
    {
        return Inertia::render('Auth/Code', [
            'status' => session('status'),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Login
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->notify(new LoginNotification);
        } else {
            sleep(1);
        }
        return to_route('login')->with('status', 'sent');
    }

    /**
     * QR CodeLogin
     */
    public function store_code(CodeRequest $request): RedirectResponse
    {
        $request->authenticate();
        $code = Code::where('code', $request->code)->first();
    
        if (!$code) {
            return to_route('code_login')->with('message', __('codes.not_valid'));
        }

        if (!$code->wasPickedUp()) {
            return to_route('code_login')->with('message', __('codes.not_enabled'));
        }

        // Register code as used
        $code->used_at = now();
        $code->save();

        Auth::login($code->user, true);
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Autologin links
     */
    public function autologin(Request $request, String $token): RedirectResponse
    {
        $loginToken = LoginToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$loginToken) {
            return to_route('login')->with('message', __('codes.link_expired'));
        }

        Auth::login($loginToken->user, true);
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Autocode links
     */
    public function autocode(Request $request, String $token): RedirectResponse
    {
        $code = Code::where('code', $token)->first();

        if (!$code) {
            return to_route('code_login')->with('message', __('codes.not_valid'));
        }

        if (!$code->wasPickedUp()) {
            return to_route('code_login')->with('message', __('codes.not_enabled'));
        }

        // Register code as used
        $code->used_at = now();
        $code->save();

        Auth::login($code->user, true);
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
