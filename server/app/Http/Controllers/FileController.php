<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\DeleteRequest;
use App\Http\Requests\File\StoreRequest;
use App\Http\Requests\File\UpdateRequest;
use App\Http\Resources\FileResource;
use App\Http\Resources\UsersFileResource;
use App\Models\AccessType;
use App\Models\File;
use App\Models\FileType;
use App\Models\User;
use App\Models\UsersFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        try {
            // Get user files
            $userFiles = UsersFile::all()->where('user_id', auth()->user()->id)->pluck('file_id');

            $files = File::whereIn('id', $userFiles)->get();

            // Return all files
            return response()->json([
                'success' => true,
                'message' => 'Files list',
                'data' => FileResource::collection($files)
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
            // Get file
            $files = $request->file('files');

            // Get user
            $user = auth()->user();

            // Files list to return
            $filesToReturn = [];

            // Create new files
            foreach ($files as $file) {
                // If file not exists
                if(!$this->checkFileNotExists($file)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File ' . $file->getClientOriginalName() . ' already exists'
                    ], 400);
                }

                // Store file
                Storage::disk('uploads')->put(auth()->user()->id, $file);

                // Get data from file
                $data = [
                    'name' => $file->hashName(),
                    'original_name' => $file->getClientOriginalName(),
                    'uri' => $user->id . '/' . $file->hashName(),
                    'current_dir' => '/',
                    'size' => $file->getSize(),
                    'file_type_id' => FileType::all()->where('name', $file->extension())->first() ?
                        FileType::all()->where('name', $file->extension())->first()->id :
                        FileType::all()->where('name', 'other')->first()->id
                ];

                // Add created file to files list
                $file = File::create($data);
                $filesToReturn[] = $file->fresh();

                // Increase user's disk space in use
                User::find($user->id)->increment('disk_space_used', $file->size);

                // Create user file in UsersFile
                UsersFile::create([
                    'user_id' => $user->id,
                    'file_id' => $file->id,
                    'access_type_id' => 2
                ]);
            }

            // Return created file
            return response()->json([
                'success' => true,
                'message' => 'File was created successfully',
                'data' => FileResource::collection($filesToReturn)
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
            // Get file
            $file = File::find($id);

            // Return file
            return response()->json([
                'success' => true,
                'message' => 'File',
                'data' => new FileResource($file)
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
            // Get validated data
            $data = $request->validated();

            // Get user
            $user = auth()->user();

            // Find file
            $file = File::find($id);


            // *********************
            // Example for uri = '/src/images' (How it will be in storage: '{PROJECT_URI}/uploads/{user_id}/src/images/image.png')
            // *********************


            // If uri if changed
            if(isset($data['uri']) && $data['uri'] !== $file->current_dir) {
                // Move file to new uri
                $storageFile = Storage::disk('uploads')->get($file->uri);

                // Store and update file
                if($data['uri'] !== '/') {
                    // File's uri
                    $fileUri = $user->id . '/' . $data['uri'] . '/' . $file->name;

                    // Store file
                    Storage::disk('uploads')->put($fileUri, $storageFile);
                    
                    // Delete old file
                    Storage::disk('uploads')->delete($file->uri);
    
                    // Update file's uri
                    $file->update([
                        'uri' => $fileUri,
                        'current_dir' => $data['uri']
                    ]);
                }

            }

            // If name if changed
            if(isset($data['name']) && $data['name'] !== $file->name) {
                // File name
                $fileName = $data['name'] . '.' . FileType::find($file->file_type_id)->name;

                // Get hashed name for file
                $hashedFileName = md5($user->id . '/' . $fileName) . '.' . FileType::find($file->file_type_id)->name;

                // Rename file in storage
                $fileFromStorage = Storage::disk('uploads')->get($file->uri);
                Storage::disk('uploads')->put($user->id . '/' . $file->current_dir . '/' . $hashedFileName, $fileFromStorage);

                // Delete old file
                Storage::disk('uploads')->delete($file->uri);

                // Update file's name
                $file->update([
                    'name' => $hashedFileName,
                    'original_name' => $fileName,
                    'uri' => $user->id . '/' . $file->current_dir . '/' . $hashedFileName
                ]);
            }

            // Return updated file
            return response()->json([
                'success' => true,
                'message' => 'File was updated',
                'data' => new FileResource($file->fresh())
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Get file
            $file = File::find($id);

            // Decrease user disk space in use
            User::find(auth()->user()->id)->decrement('disk_space_used', $file->size);

            // If delete processing is failed
            if (!$file->delete()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File has not to be deleted'
                ], 500);
            }

            // If delete processing is successful
            return response()->json([
                'success' => true,
                'message' => 'File was delete successfully'
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }


    protected function checkFileNotExists($file): bool
    {
        // Get user files
        $userFilesIds = UsersFile::all()->where('user_id', auth()->user()->id)->pluck('file_id');
        $userFilesOrigNames = File::all()->whereIn('id', $userFilesIds)->pluck('original_name');

        // Check file exists
        if(!$userFilesOrigNames->contains($file->getClientOriginalName())) {
            return true;
        }

        return false;
    }
}
