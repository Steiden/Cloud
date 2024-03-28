<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest as UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        try {
            // Get all users
            $users = User::all();

            // Return all users
            return response()->json([
                'success' => true,
                'message' => 'Users list',
                'data' => UserResource::collection($users),
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            // Get validated data from request
            $data = $request->validated();

            // Create new user
            $user = User::create($data);

            // If create processing is successful
            return response()->json([
                'success' => true,
                'message' => 'User was created successfully',
                'data' => new UserResource($user->fresh()),
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Find user
            $user = User::findOrFail($id);

            // If user not found
            if (!isset ($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Return user
            return response()->json([
                'success' => true,
                'message' => 'User',
                'data' => new UserResource($user),
            ], 200);

        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
            ], 200);
        }
    }

    public function update(UserUpdateRequest $request, $id)
    {
        try {
            // Get validated data from request
            $data = $request->validated();

            // Find user
            $user = User::findOrFail($id);

            // If update processing is failed
            if (!$user->update($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User update failed',
                ], 500);
            }

            // If update processing is successful
            return response()->json([
                'success' => true,
                'message' => 'User update successfully',
                'data' => new UserResource($user),
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find user
            $user = User::findOrFail($id);

            // If delete processing is failed
            if (!$user->deleteOrFail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // If delete processing is successful
            return response()->json([
                'success' => true,
                'message' => 'User was deleted successfully',
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
            ], 500);
        }
    }
}
