<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class CompanyController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user()->load('roles', 'company');

        if (! $user->roles->contains('name', 'company')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $company = $user->company;

        if (! $company) {
            return response()->json(['error' => 'Perfil no encontrado'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'role' => 'company',
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'description' => $company->description,
                'address' => $company->address,
                'city' => $company->city,
                'phone' => $company->phone,
                'verified' => $company->verified
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user()->load('roles', 'company');

        if (! $user->roles->contains('name', 'company')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100'
        ]);

        DB::transaction(function () use ($user, $validated) {
            // actualizar email del usuario
            $user->update([
                'email' => $validated['email']
            ]);

            $companyData = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'phone' => $validated['phone'] ?? null
            ];

            if ($user->company) {
                $user->company->update($companyData);
            } else {
                $company = new Company($companyData);
                $user->company()->save($company);
            }
        });

        $company = $user->company()->first();

        return response()->json([
            'message' => 'Perfil actualizado',
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'description' => $company->description,
                'address' => $company->address,
                'city' => $company->city,
                'phone' => $company->phone
            ],
            'email' => $user->email
        ]);
    }
}