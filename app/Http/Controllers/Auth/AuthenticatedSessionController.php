<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ApiLoginRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\TransientToken;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }

    /**
     * Handle an incoming authentication request.
     */
    public function storeApi(ApiLoginRequest $request): Response
    {
        $request->authenticate();

        $user = $request->user();

        $token = $user->createToken($request->get('device_name'))->plainTextToken;

        return response([
            'token' => $token
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroyApi(Request $request): Response
    {
        $token = $request->user()->currentAccessToken();

        abort_unless($token instanceof PersonalAccessToken, 403);

        $token->delete();

        return response()->noContent();
    }
}
