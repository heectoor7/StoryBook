<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CompanyController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        $user = $request->user()->load(['roles', 'company']);
        return response()->json($user);
    });

    // Posts from companies that the authenticated user follows
    Route::get('/user/followed-companies-posts', function (Request $request) {
        $user = $request->user();

        // Obtener company_ids que sigue el usuario
        $companyIds = \DB::table('followers')->where('user_id', $user->id)->pluck('company_id');

        if ($companyIds->isEmpty()) {
            return response()->json([]);
        }

        $posts = App\Models\Post::with('company')
            ->whereIn('company_id', $companyIds)
            ->where(function($q) {
                // incluir posts normales y stories no expiradas
                $q->where('is_story', false)
                  ->orWhere(function($q2) {
                      $q2->where('is_story', true)
                         ->where('expires_at', '>', now());
                  });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($p){
                return [
                    'id' => $p->id,
                    'company_id' => $p->company_id,
                    'company_name' => $p->company->name ?? null,
                    'content' => $p->content,
                    'is_story' => (bool) $p->is_story,
                    'expires_at' => $p->expires_at,
                    'created_at' => $p->created_at
                ];
            });

        return response()->json($posts);
    });

    Route::post('/services', [ServiceController::class, 'store']);

    // Rutas para el panel de empresa
    Route::prefix('company')->group(function () {
        // Estadísticas
        Route::get('/stats', [CompanyController::class, 'getStats']);
        
        // Posts y Stories
        Route::get('/posts', [CompanyController::class, 'getPosts']);
        Route::post('/posts', [CompanyController::class, 'createPost']);
        Route::delete('/posts/{id}', [CompanyController::class, 'deletePost']);
        
        // Servicios
        Route::get('/services', [CompanyController::class, 'getServices']);
        Route::get('/services/{id}', [CompanyController::class, 'getService']);
        Route::post('/services', [CompanyController::class, 'saveService']);
        Route::put('/services/{id}', [CompanyController::class, 'saveService']);
        Route::delete('/services/{id}', [CompanyController::class, 'deleteService']);
        
        // Reservas
        Route::get('/bookings', [CompanyController::class, 'getBookings']);
        Route::put('/bookings/{id}/status', [CompanyController::class, 'updateBookingStatus']);
    });
});
