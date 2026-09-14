<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {}

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(LoginRequest $request)
    {
        // Validación automática en LoginRequest
        $this->authService->authenticate(
            $request->username,
            $request->password,
            $request->boolean('remember')
        );

        $request->session()->regenerate();

        return redirect()->intended($this->authService->redirectToDashboard());
    }

    public function logout(Request $request)
    {
        $this->authService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
