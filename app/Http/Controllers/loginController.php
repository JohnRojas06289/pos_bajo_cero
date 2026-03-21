<?php

namespace App\Http\Controllers;

use App\Http\Requests\loginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class loginController extends Controller
{
    public function __construct()
    {
        $this->middleware('check-user-estado', ['only' => ['login']]);
    }

    public function index(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('panel');
        }
        return view('auth.login');
    }

    public function login(loginRequest $request): RedirectResponse
    {
        $key = 'login:' . $request->ip();

        // Protección contra fuerza bruta: máx 5 intentos por minuto por IP
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->to('login')->withErrors(
                "Demasiados intentos fallidos. Intenta de nuevo en {$seconds} segundos."
            );
        }

        // Validar credenciales
        if (!Auth::validate($request->only('email', 'password'))) {
            RateLimiter::hit($key, 60);
            return redirect()->to('login')->withErrors('Credenciales incorrectas');
        }

        // Login exitoso — limpiar intentos
        RateLimiter::clear($key);

        $user = Auth::getProvider()->retrieveByCredentials($request->only('email', 'password'));
        Auth::login($user);

        return redirect()->route('panel')->with('login', 'Bienvenido ' . $user->name);
    }
}
