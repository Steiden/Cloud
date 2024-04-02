<?php

namespace App\Http\Middleware;

use App\Models\File;
use App\Models\FileType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRequestFileExtensionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get file from request
        $file = $request->file('file');

        // Get file from database
        $fileModel = File::find($request->route('id'));

        // If request file extension is not equal to file extension from database
        if($file->getClientOriginalExtension() !== FileType::find($fileModel->file_type_id)->name) {
            return response()->json([
                'success' => false,
                'message' => 'File extension is not valid'
            ], 400);
        }

        return $next($request);
    }
}
