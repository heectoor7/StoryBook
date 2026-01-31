<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        $user = $request->user()->load('roles');
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

    // Services from companies that the authenticated user follows
    Route::get('/user/followed-companies-services', function (Request $request) {
        $user = $request->user();
        $companyIds = \DB::table('followers')->where('user_id', $user->id)->pluck('company_id');

        if ($companyIds->isEmpty()) {
            return response()->json([]);
        }

        $services = App\Models\Service::with(['company','category'])
            ->whereIn('company_id', $companyIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($s) {
                return [
                    'id' => $s->id,
                    'company_id' => $s->company_id,
                    'company_name' => $s->company->name ?? null,
                    'category' => $s->category->name ?? null,
                    'name' => $s->name,
                    'description' => $s->description,
                    'price' => $s->price,
                ];
            });

        return response()->json($services);
    });

    // All services (public to authenticated users)
    Route::get('/services', function (Request $request) {
        $services = App\Models\Service::with(['company','category'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($s) {
                return [
                    'id' => $s->id,
                    'company_id' => $s->company_id,
                    'company_name' => $s->company->name ?? null,
                    'category' => $s->category->name ?? null,
                    'name' => $s->name,
                    'description' => $s->description,
                    'price' => $s->price,
                ];
            });

        return response()->json($services);
    });

    // Bookings for authenticated user
    Route::get('/user/bookings', function (Request $request) {
        $user = $request->user();
        $bookings = App\Models\Booking::with(['service.company'])
            ->where('user_id', $user->id)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get()
            ->map(function($b){
                return [
                    'id' => $b->id,
                    'service_id' => $b->service_id,
                    'service_name' => $b->service->name ?? null,
                    'company_name' => $b->service->company->name ?? null,
                    'date' => $b->date,
                    'time' => $b->time,
                    'status' => $b->status
                ];
            });

        return response()->json($bookings);
    });

    // Logout: eliminar token actual
    Route::post('/logout', function (Request $request) {
        if ($request->user()) {
            $token = $request->user()->currentAccessToken();
            if ($token) $token->delete();
        }
        return response()->json(['message' => 'Logout correcto']);
    });

    Route::post('/services', [ServiceController::class, 'store']);
});
