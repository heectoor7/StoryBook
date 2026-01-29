<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
}