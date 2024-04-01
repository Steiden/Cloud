<?php

namespace App\Http\Middleware;

use App\Models\File;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsOwnerOfFileForWriteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Find file 
        $file = File::find($request->route('id'));

        // Check if user is owner of file for move it
        if (isset($request->uri) && $file->owner !== auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not owner of this file for move it'
            ], 403);
        }

        return $next($request);
    }
}
