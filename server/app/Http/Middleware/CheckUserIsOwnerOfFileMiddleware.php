<?php

namespace App\Http\Middleware;

use App\Models\File;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsOwnerOfFileMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get file's id
        $fileId = $request->file_id ? $request->file_id : $request->route('id');

        // Find file
        $file = File::find($fileId);

        // Check if user is owner of file finded
        if($file->owner !== auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not owner of this file'
            ], 403);
        }

        return $next($request);
    }
}
