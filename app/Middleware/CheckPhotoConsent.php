<?php
// app/Http/Middleware/CheckPhotoConsent.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\ConsentController;
use Symfony\Component\HttpFoundation\Response;

class CheckPhotoConsent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check consent for photo upload routes
        if ($request->routeIs('photo.upload') || $request->routeIs('teacher.training.photo.store')) {
            
            // Get child_id from request (you might need to adjust this based on your form structure)
            $childId = $request->input('child_id');
            
            if ($childId && !ConsentController::hasPhotoConsent($childId)) {
                return redirect()->back()
                    ->with('error', 'Photo consent is required for this child before uploading photos.');
            }
        }

        return $next($request);
    }
}

// Don't forget to register this middleware in app/Http/Kernel.php
// Add to $middlewareAliases array:
// 'photo.consent' => \App\Http\Middleware\CheckPhotoConsent::class,