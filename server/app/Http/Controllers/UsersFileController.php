<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersFile\StoreRequest;
use App\Http\Requests\UsersFile\UpdateRequest;
use App\Http\Resources\UsersFileResource;
use App\Models\UsersFile;
use Exception;
use Illuminate\Http\Request;

class UsersFileController extends Controller
{
    public function index()
    {
        try {
            // Get all users files
            $usersFiles = UsersFile::all();

            // Return users files
            return response()->json([
                'success' => true,
                'message' => 'Users files list',
                'data' => UsersFileResource::collection($usersFiles)
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            // Get validated data
            $data = $request->validated();

            // Create new users file if not exists
            if (!($usersFile = UsersFile::all()->where('user_id', $data['user_id'])->where('file_id', $data['file_id'])->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Users file already exists'
                ], 404);
            }

            // Return users file
            return response()->json([
                'success' => true,
                'message' => 'Users file was created successfully',
                'data' => new UsersFileResource($usersFile->fresh())
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Get users file
            $usersFile = UsersFile::findOrFail($id);

            // If users file not found
            if (!isset ($usersFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Users file not found'
                ], 404);
            }

            // Return users file
            return response()->json([
                'success' => true,
                'message' => 'Users file',
                'data' => new UsersFileResource($usersFile)
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            // Find users file
            $usersFile = UsersFile::findOrFail($id);

            // If users file not found
            if (!isset ($usersFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Users file not found'
                ], 404);
            }

            // If users file update processing is failed
            if (!$usersFile->updateOrFail($request->validated())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Users file update failed'
                ], 500);
            }

            // Return updated users file
            return response()->json([
                'success' => true,
                'message' => 'Users file was updated successfully',
                'data' => new UsersFileResource($usersFile->fresh())
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function detroy($id)
    {
        try {
            // Find users file
            $usersFile = UsersFile::find($id);

            // If userse file not found
            if (!isset ($usersFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Users file not found'
                ], 404);
            }

            // If users file delete processing is failed
            if (!$usersFile->deleteOrFail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Users file delete failed'
                ], 500);
            }

            // Return message
            return response()->json([
                'success' => true,
                'message' => 'Users file was deleted successfully'
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
}
