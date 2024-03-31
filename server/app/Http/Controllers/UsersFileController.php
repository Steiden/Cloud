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
            // Get all user-file relations
            $usersFiles = UsersFile::all()->where('user_id', auth()->user()->id);

            // Return user-file relations
            return response()->json([
                'success' => true,
                'message' => 'User-files relations',
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

            // Create new user-file relation if not exists
            if (UsersFile::all()->where('user_id', $data['user_id'])->where('file_id', $data['file_id'])->first()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User-file relation already exists'
                ], 400);
            }
            $usersFile = UsersFile::create($data);

            // Return user-file relation
            return response()->json([
                'success' => true,
                'message' => 'User-file relation was created successfully',
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
            // Get user-file relation
            $usersFile = UsersFile::findOrFail($id);

            // Return user-file relation
            return response()->json([
                'success' => true,
                'message' => 'User-file relation',
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
            // Find user-file relation
            $usersFile = UsersFile::findOrFail($id);

            // If user-file relation update processing is failed
            if (!$usersFile->updateOrFail($request->validated())) {
                return response()->json([
                    'success' => false,
                    'message' => 'User-file relation was updated failed'
                ], 500);
            }

            // Return updated users file
            return response()->json([
                'success' => true,
                'message' => 'User-file relation was updated successfully',
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
            // Find user-file relation
            $usersFile = UsersFile::find($id);

            // If user-file relation delete processing is failed
            if (!$usersFile->deleteOrFail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User-file relation was deleted failed'
                ], 500);
            }

            // Return message
            return response()->json([
                'success' => true,
                'message' => 'User-file relation was deleted successfully'
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }
}
