<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|integer|exists:categories,id'
        ]);

        $user = Auth::user();

        // Validar que el usuario es company
        if (!$user->hasRole('company')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $company = $user->company;

        $service = Service::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id
        ]);

        return response()->json([
            'message' => 'Servicio creado correctamente',
            'service' => $service
        ]);
    }
}