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

        $posts = App\Models\Post::with(['company', 'comments.user'])
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
                    'company_logo' => $p->company->logo ?? null,
                    'content' => $p->content,
                    'image' => $p->image,
                    'is_story' => (bool) $p->is_story,
                    'expires_at' => $p->expires_at,
                    'created_at' => $p->created_at,
                    'comments' => $p->comments->map(function($c) {
                        return [
                            'id' => $c->id,
                            'user_name' => $c->user->name ?? 'Anonymous',
                            'content' => $c->content,
                            'created_at' => $c->created_at
                        ];
                    })
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
                    'company_logo' => $s->company->logo ?? null,
                    'category' => $s->category->name ?? null,
                    'name' => $s->name,
                    'description' => $s->description,
                    'image' => $s->image,
                    'price' => $s->price,
                ];
            });

        return response()->json($services);
    });

    // All services (public to authenticated users)
    Route::get('/services', function (Request $request) {
        $search = $request->query('search');
        
        $query = App\Models\Service::with(['company','category']);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('company', function($q2) use ($search) {
                      $q2->where('name', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('category', function($q2) use ($search) {
                      $q2->where('name', 'LIKE', '%' . $search . '%');
                  });
            });
        }
        
        $services = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function($s) {
                return [
                    'id' => $s->id,
                    'company_id' => $s->company_id,
                    'company_name' => $s->company->name ?? null,
                    'company_logo' => $s->company->logo ?? null,
                    'category' => $s->category->name ?? null,
                    'name' => $s->name,
                    'description' => $s->description,
                    'image' => $s->image,
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

    // Create new booking
    Route::post('/user/bookings', function (Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required'
        ]);

        // Verificar si ya existe una reserva en esa fecha/hora para ese servicio
        $exists = App\Models\Booking::where('service_id', $validated['service_id'])
            ->where('date', $validated['date'])
            ->where('time', $validated['time'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'A booking already exists for this time slot'], 422);
        }

        $booking = App\Models\Booking::create([
            'user_id' => $user->id,
            'service_id' => $validated['service_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'status' => 'PENDING'
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking
        ], 201);
    });

    // Update booking
    Route::put('/user/bookings/{id}', function (Request $request, $id) {
        $user = $request->user();
        $booking = App\Models\Booking::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required'
        ]);

        // Verificar si ya existe una reserva en esa fecha/hora para ese servicio
        $exists = App\Models\Booking::where('service_id', $booking->service_id)
            ->where('date', $validated['date'])
            ->where('time', $validated['time'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Ya existe una reserva para ese horario'], 422);
        }

        $booking->date = $validated['date'];
        $booking->time = $validated['time'];
        $booking->save();

        return response()->json(['message' => 'Reserva actualizada correctamente', 'booking' => $booking]);
    });

    // Delete booking
    Route::delete('/user/bookings/{id}', function (Request $request, $id) {
        $user = $request->user();
        $booking = App\Models\Booking::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        $booking->delete();

        return response()->json(['message' => 'Reserva eliminada correctamente']);
    });

    // Comments - Add comment to post
    Route::post('/posts/{postId}/comments', function (Request $request, $postId) {
        $request->validate([
            'content' => 'required|string|max:500'
        ]);

        $post = App\Models\Post::findOrFail($postId);
        
        $comment = App\Models\Comment::create([
            'post_id' => $postId,
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'created_at' => now()
        ]);

        $comment->load('user');

        return response()->json([
            'message' => 'Comment added',
            'comment' => [
                'id' => $comment->id,
                'user_name' => $comment->user->name,
                'content' => $comment->content,
                'created_at' => $comment->created_at
            ]
        ]);
    });

    Route::delete('/comments/{id}', function (Request $request, $id) {
        $comment = App\Models\Comment::findOrFail($id);
        
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted']);
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
    
    // Ratings - Get ratings for a service
    Route::get('/services/{serviceId}/ratings', function ($serviceId) {
        $ratings = App\Models\Rating::with('user')
            ->where('service_id', $serviceId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($r) {
                return [
                    'id' => $r->id,
                    'user_id' => $r->user_id,
                    'user_name' => $r->user->name ?? 'Anonymous',
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'created_at' => $r->created_at
                ];
            });
        
        return response()->json($ratings);
    });
    
    // Ratings - Add rating to service
    Route::post('/services/{serviceId}/ratings', function (Request $request, $serviceId) {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);
        
        $service = App\Models\Service::findOrFail($serviceId);
        
        // Verificar si el usuario ya ha valorado este servicio
        $existingRating = App\Models\Rating::where('service_id', $serviceId)
            ->where('user_id', $request->user()->id)
            ->first();
        
        if ($existingRating) {
            // Actualizar rating existente
            $existingRating->rating = $request->rating;
            $existingRating->comment = $request->comment;
            $existingRating->save();
            
            return response()->json([
                'message' => 'Rating updated successfully',
                'rating' => [
                    'id' => $existingRating->id,
                    'user_name' => $request->user()->name,
                    'rating' => $existingRating->rating,
                    'comment' => $existingRating->comment,
                    'created_at' => $existingRating->created_at
                ]
            ]);
        }
        
        // Crear nuevo rating
        $rating = App\Models\Rating::create([
            'service_id' => $serviceId,
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'created_at' => now()
        ]);
        
        $rating->load('user');
        
        return response()->json([
            'message' => 'Rating added successfully',
            'rating' => [
                'id' => $rating->id,
                'user_name' => $rating->user->name,
                'rating' => $rating->rating,
                'comment' => $rating->comment,
                'created_at' => $rating->created_at
            ]
        ]);
    });
});