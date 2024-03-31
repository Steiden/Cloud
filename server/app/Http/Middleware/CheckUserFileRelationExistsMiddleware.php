<?php

namespace App\Http\Middleware;

use App\Models\UsersFile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserFileRelationExistsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get user-file relation
        $userFileRelation = UsersFile::find($request->route('id'));

        // Return if user-file relation not exists
        if (!$userFileRelation) {
            return response()->json([
                'success' => false,
                'message' => 'User-file relation not found'
            ], 404);
        }

        return $next($request);
    }
}
