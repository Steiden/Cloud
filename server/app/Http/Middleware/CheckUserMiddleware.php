<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get id
        $id = $request->route('id');

        // Get user
        $user = $request->user();

        // If user not found
        if (!isset ($user)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // If user to delete not found
        if (!User::find($id)) {
            return response()->json(['success' => false, 'message' => 'User to delete not found']);
        }

        // If user id and request id is not equal
        if ($user->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Not for this user'
            ], 404);
        }

        return $next($request);
    }
}
