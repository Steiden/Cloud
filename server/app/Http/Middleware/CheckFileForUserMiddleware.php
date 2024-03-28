<?php

namespace App\Http\Middleware;

use App\Models\UsersFile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFileForUserMiddleware
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
            
        // If file unavailable
        if (!UsersFile::all()->where('user_id', auth()->user()->id)->where('file_id', $id)->first()) {
            return response()->json([
                'success' => false,
                'message' => 'File unavailable'
            ], 403);
        }

        return $next($request);
    }
}
