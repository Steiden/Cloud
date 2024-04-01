<?php

namespace App\Http\Middleware;

use App\Models\File;
use App\Models\UsersFile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserCanWriteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get file's id
        $fileId = $request->route('id');

        // Find file
        $file = File::find($fileId);

        // Check if user can write this file
        if (!UsersFile::all()->where('user_id', auth()->user()->id)->where('file_id', $fileId)->where('access_type_id', 2)->first()) {
            return response()->json([
                'success' => false,
                'message' => 'User cannot write this file'
            ], 403);
        }

        return $next($request);
    }
}
