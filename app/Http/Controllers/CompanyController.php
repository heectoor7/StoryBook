<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use App\Models\Post;
use App\Models\Service;
use App\Models\Booking;

class CompanyController extends Controller
{
    private function resolveCompanyForPosts(Request $request): ?Company
    {
        $user = $request->user()->loadMissing('roles');

        if (! $user->roles->contains('name', 'company')) {
            return null;
        }

        return Company::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->name]
        );
    }

    // ========== MÉTODOS DE PERFIL ==========
    
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
                'verified' => $company->verified,
                'logo' => $company->logo
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
            'city' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120'
        ]);

        DB::transaction(function () use ($user, $validated, $request) {
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

            // Manejar la carga de imagen si existe
            if ($request->hasFile('logo')) {
                // Eliminar imagen anterior si existe
                if ($user->company && $user->company->logo) {
                    Storage::delete($user->company->logo);
                }

                // Guardar nueva imagen
                $path = $request->file('logo')->store('company_profiles', 'public');
                $companyData['logo'] = '/storage/' . $path;
            }

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
                'phone' => $company->phone,
                'logo' => $company->logo
            ],
            'email' => $user->email
        ]);
    }

    // ========== MÉTODOS DEL PANEL DE EMPRESA ==========
    
    /**
     * Obtener estadísticas de la empresa
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        
        // Buscar la empresa del usuario autenticado
        $company = Company::where('user_id', $user->id)->first();
        
        if (!$company) {
            return response()->json(['error' => 'No se encontró empresa asociada'], 404);
        }

        $stats = [
            'total_services' => Service::where('company_id', $company->id)->count(),
            'total_bookings' => Booking::where('company_id', $company->id)->count(),
            'total_followers' => $company->followers()->count(),
            'average_rating' => round($company->ratings()->avg('score') ?? 0, 1),
        ];

        return response()->json($stats);
    }

    /**
     * Obtener todos los posts de la empresa
     */
    public function getPosts(Request $request)
    {
        $company = $this->resolveCompanyForPosts($request);
        
        if (!$company) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $posts = Post::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($posts);
    }

    /**
     * Crear un nuevo post o story
     */
    public function createPost(Request $request)
    {
        $company = $this->resolveCompanyForPosts($request);
        
        if (!$company) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'content' => 'nullable|string',
            'is_story' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048'
        ]);

        $post = new Post();
        $post->company_id = $company->id;
        $post->content = $request->input('content');
        $post->is_story = $request->input('is_story', false);
        
        // Si es story, establecer fecha de expiración (24 horas)
        if ($post->is_story) {
            $post->expires_at = now()->addHours(24);
        }

        // Manejo de imagen
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $post->image = '/storage/' . $path;
        }

        $post->save();

        return response()->json($post, 201);
    }

    /**
     * Eliminar un post
     */
    public function deletePost(Request $request, $id)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user->id)->first();
        
        if (!$company) {
            return response()->json(['error' => 'No se encontró empresa asociada'], 404);
        }

        $post = Post::where('id', $id)
            ->where('company_id', $company->id)
            ->first();

        if (!$post) {
            return response()->json(['error' => 'Post no encontrado'], 404);
        }

        // Eliminar imagen si existe
        if ($post->image) {
            $imagePath = str_replace('/storage/', '', $post->image);
            Storage::disk('public')->delete($imagePath);
        }

        $post->delete();

        return response()->json(['message' => 'Post eliminado exitosamente']);
    }

    /**
     * Obtener todos los servicios de la empresa
     */
    public function getServices(Request $request)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user->id)->first();
        
        if (!$company) {
            return response()->json(['error' => 'No se encontró empresa asociada'], 404);
        }

        $services = Service::where('company_id', $company->id)
            ->with('category')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'price' => $service->price,
                    'category' => $service->category->name ?? null,
                    'image' => $service->image,
                    'created_at' => $service->created_at,
                ];
            });

        return response()->json($services);
    }

    /**
     * Obtener un servicio específico
     */
    public function getService(Request $request, $id)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user->id)->first();
        
        if (!$company) {
            return response()->json(['error' => 'No se encontró empresa asociada'], 404);
        }

        $service = Service::where('id', $id)
            ->where('company_id', $company->id)
            ->with('category')
            ->first();

        if (!$service) {
            return response()->json(['error' => 'Servicio no encontrado'], 404);
        }

        return response()->json([
            'id' => $service->id,
            'name' => $service->name,
            'description' => $service->description,
            'price' => $service->price,
            'category' => $service->category->name ?? '',
            'category_id' => $service->category_id,
            'image' => $service->image,
        ]);
    }

    /**
     * Crear o actualizar un servicio
     */
    public function saveService(Request $request, $id = null)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user->id)->first();
        
        if (!$company) {
            return response()->json(['error' => 'No se encontró empresa asociada'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        // Si es edición, buscar el servicio
        if ($id) {
            $service = Service::where('id', $id)
                ->where('company_id', $company->id)
                ->first();
            
            if (!$service) {
                return response()->json(['error' => 'Servicio no encontrado'], 404);
            }
        } else {
            $service = new Service();
            $service->company_id = $company->id;
        }

        $service->name = $request->input('name');
        $service->description = $request->input('description');
        $service->price = $request->input('price');

        // Manejar categoría
        if ($request->has('category') && $request->input('category')) {
            $categoryName = $request->input('category');
            $category = \App\Models\Category::firstOrCreate(['name' => $categoryName]);
            $service->category_id = $category->id;
        }

        // Manejo de imagen
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($service->image) {
                $imagePath = str_replace('/storage/', '', $service->image);
                Storage::disk('public')->delete($imagePath);
            }
            
            $path = $request->file('image')->store('services', 'public');
            $service->image = '/storage/' . $path;
        }

        $service->save();

        return response()->json($service, $id ? 200 : 201);
    }

    /**
     * Eliminar un servicio
     */
    public function deleteService(Request $request, $id)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user->id)->first();
        
        if (!$company) {
            return response()->json(['error' => 'No se encontró empresa asociada'], 404);
        }

        $service = Service::where('id', $id)
            ->where('company_id', $company->id)
            ->first();

        if (!$service) {
            return response()->json(['error' => 'Servicio no encontrado'], 404);
        }

        // Eliminar imagen si existe
        if ($service->image) {
            $imagePath = str_replace('/storage/', '', $service->image);
            Storage::disk('public')->delete($imagePath);
        }

        $service->delete();

        return response()->json(['message' => 'Servicio eliminado exitosamente']);
    }

    /**
     * Obtener todas las reservas de la empresa
     */
    public function getBookings(Request $request)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user->id)->first();
        
        if (!$company) {
            return response()->json(['error' => 'No se encontró empresa asociada'], 404);
        }

        // Obtener IDs de servicios de la empresa
        $serviceIds = Service::where('company_id', $company->id)->pluck('id');

        $bookings = Booking::whereIn('service_id', $serviceIds)
            ->with(['user', 'service'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'user_name' => $booking->user->name ?? 'Usuario',
                    'service_name' => $booking->service->name ?? 'Servicio',
                    'booking_date' => $booking->date . ' ' . $booking->time,
                    'date' => $booking->date,
                    'time' => $booking->time,
                    'status' => $booking->status,
                    'notes' => $booking->notes ?? '',
                ];
            });

        return response()->json($bookings);
    }

    /**
     * Actualizar el estado de una reserva
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user->id)->first();
        
        if (!$company) {
            return response()->json(['error' => 'No se encontró empresa asociada'], 404);
        }

        $request->validate([
            'status' => 'required|in:PENDING,CONFIRMED,CANCELLED'
        ]);

        // Obtener IDs de servicios de la empresa
        $serviceIds = Service::where('company_id', $company->id)->pluck('id');

        $booking = Booking::where('id', $id)
            ->whereIn('service_id', $serviceIds)
            ->first();

        if (!$booking) {
            return response()->json(['error' => 'Reserva no encontrada'], 404);
        }

        $booking->status = $request->input('status');
        $booking->save();

        return response()->json(['message' => 'Estado actualizado exitosamente', 'booking' => $booking]);
    }

    /**
     * Obtener seguidores de la empresa
     */
    public function getFollowers(Request $request)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user->id)->first();
        
        if (!$company) {
            return response()->json(['error' => 'No se encontró empresa asociada'], 404);
        }

        $followers = \DB::table('followers')
            ->join('users', 'followers.user_id', '=', 'users.id')
            ->where('followers.company_id', $company->id)
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        return response()->json($followers);
    }

    /**
     * Eliminar seguidor de la empresa
     */
    public function removeFollower(Request $request, $userId)
    {
        $user = $request->user();
        $company = Company::where('user_id', $user->id)->first();

        if (!$company) {
            return response()->json(['error' => 'No se encontró empresa asociada'], 404);
        }

        $deleted = \DB::table('followers')
            ->where('company_id', $company->id)
            ->where('user_id', $userId)
            ->delete();

        if (!$deleted) {
            return response()->json(['error' => 'Seguidor no encontrado'], 404);
        }

        return response()->json(['message' => 'Follower removed successfully']);
    }
}
