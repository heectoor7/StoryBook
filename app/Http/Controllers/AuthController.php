<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Mail\RegistroExitosoMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Throwable;

class AuthController extends Controller
{
    // REGISTRO
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:user,company'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $role = Role::where('name', $request->role)->firstOrFail(); // ← Falla si no existe
        $user->roles()->attach($role->id);

        if ($role->name === 'company') {
            Company::firstOrCreate(
                ['user_id' => $user->id],
                ['name' => $user->name]
            );
        }

        try {
            Mail::to($user->email)->send(new RegistroExitosoMail($user));
        } catch (Throwable $exception) {
            Log::error('No se pudo enviar el correo de registro', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
        }

        // Iniciar sesión y generar token para APIs (Sanctum)
        Auth::login($user);
        $user->load('roles');
        $token = $user->createToken('auth_token')->plainTextToken;

        // Determinar rol principal con prioridad admin > company > user
        if ($user->roles->contains('name', 'admin')) {
            $mainRole = 'admin';
        } elseif ($user->roles->contains('name', 'company')) {
            $mainRole = 'company';
        } else {
            $mainRole = 'user';
        }

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user,
            'role' => $mainRole,
            'token' => $token
        ]);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $user->load('roles');
            $token = $user->createToken('auth_token')->plainTextToken;

            // Determinar rol principal (admin > company > user)
            if ($user->roles->contains('name', 'admin')) {
                $role = 'admin';
            } elseif ($user->roles->contains('name', 'company')) {
                $role = 'company';
            } else {
                $role = 'user';
            }

            return response()->json([
                'message' => 'Login correcto',
                'user' => $user,
                'role' => $role,
                'token' => $token
            ]);
        } else {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => 'We have sent you an email to reset your password.'
                ]);
            }

            return response()->json([
                'message' => 'If the email is registered, you will receive a password reset link.'
            ]);
        } catch (Throwable $exception) {
            Log::error('No se pudo enviar el correo de recuperación', [
                'email' => $request->email,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Email configuration error. Please verify SMTP settings and try again.'
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password reset successfully.'
            ]);
        }

        return response()->json([
            'message' => 'The password reset link is invalid or has expired.'
        ], 422);
    }
}