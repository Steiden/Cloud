<?php

namespace App\Http\Middleware;

use App\Models\File;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFileExistsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get file id
        $id = $request->route('id');

        // If file not found
        if(!File::find($id)) {
            return response()->json([
                'succes' => false,
                'message' => 'File not found'
            ], 404);
        }

        return $next($request);
    }
}
