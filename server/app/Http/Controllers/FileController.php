<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\DeleteRequest;
use App\Http\Requests\File\StoreRequest;
use App\Http\Requests\File\UpdateContentRequest;
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
            // Get data from request
            $files = $request->file('files');

            // Get user
            $user = auth()->user();

            // Files list to return
            $filesToReturn = [];

            // Create new files
            foreach ($files as $file) {
                // If file not exists
                if (!$this->checkFileNotExists($file)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File ' . $file->getClientOriginalName() . ' already exists'
                    ], 400);
                }

                // File's uri
                $currentDir = $request->current_dir;
                $fileUri = $currentDir ? $user->id . '/' . $currentDir . '/' : $user->id . '/';

                // Store file
                Storage::disk('uploads')->put($fileUri, $file);

                // Get data from file
                $dataForFile = [
                    'name' => $file->hashName(),
                    'original_name' => $file->getClientOriginalName(),
                    'uri' => $fileUri . $file->hashName(),
                    'current_dir' => $currentDir ? $currentDir : '/',
                    'size' => $file->getSize(),
                    'file_type_id' => FileType::all()->where('name', $file->extension())->first() ?
                        FileType::all()->where('name', $file->extension())->first()->id :
                        FileType::all()->where('name', 'other')->first()->id,
                    'owner' => $user->id
                ];

                // Add created file to files list
                $file = File::create($dataForFile);
                $filesToReturn[] = $file->fresh();

                // Increase user's disk space in use
                User::find($user->id)->increment('disk_space_used', $file->size);

                // Create user-file relation in UsersFile
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
            $fileModel = File::find($id);

            // If user is not owner of file, local 'user' can be changed to 'owner' user
            if ($fileModel->owner !== $user->id) {
                $user = User::find($fileModel->owner);
            }

            // If nothing to update
            if (
                (isset($data['uri']) && $data['uri'] === $fileModel->current_dir) ||
                (isset($data['uri']) && $data['uri'] === '.' && $fileModel->current_dir === '/') ||
                (isset($data['name']) && $data['name'] === $fileModel->original_name)
            ) {

                return response()->json([
                    'success' => true,
                    'message' => 'Nothing to update'
                ], 400);
            }


            // *********************
            // Example for uri = '/src/images' (How it will be in storage: '{PROJECT_URI}/uploads/{user_id}/src/images/image.png')
            // *********************


            // If uri is changed
            if (isset($data['uri']) && $data['uri'] !== $fileModel->current_dir) {
                // Move file to new uri
                $storageFile = Storage::disk('uploads')->get($fileModel->uri);

                // Store and update file
                if ($data['uri'] !== '/') {
                    // File's uri
                    $fileUri = $data['uri'] === '.' ? $user->id . '/' . $fileModel->name : $user->id . '/' . $data['uri'] . '/' . $fileModel->name;

                    // Store file
                    Storage::disk('uploads')->put($fileUri, $storageFile);

                    // Delete old file
                    Storage::disk('uploads')->delete($fileModel->uri);

                    // Update file's uri
                    $fileModel->update([
                        'uri' => $fileUri,
                        'current_dir' => $data['uri'] === '.' ? '/' : $data['uri']
                    ]);
                }

            }

            // If name is changed
            if (isset($data['name']) && $data['name'] !== $fileModel->name) {
                // File name
                $fileName = $data['name'] . '.' . FileType::find($fileModel->file_type_id)->name;

                // Get hashed name for file
                $hashedFileName = md5($user->id . '/' . $fileName) . '.' . FileType::find($fileModel->file_type_id)->name;

                // Rename file in storage
                $fileFromStorage = Storage::disk('uploads')->get($fileModel->uri);
                Storage::disk('uploads')->put($user->id . '/' . $fileModel->current_dir . '/' . $hashedFileName, $fileFromStorage);

                // Delete old file
                Storage::disk('uploads')->delete($fileModel->uri);

                // Update file's name
                $fileModel->update([
                    'name' => $hashedFileName,
                    'original_name' => $fileName,
                    'uri' => $user->id . '/' . $fileModel->current_dir . '/' . $hashedFileName
                ]);
            }

            // Return updated file
            return response()->json([
                'success' => true,
                'message' => 'File was updated',
                'data' => new FileResource($fileModel->fresh())
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

            // Delete file from storage
            Storage::disk('uploads')->delete($file->uri);

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

    public function updateContent(UpdateContentRequest $request, $id)
    {
        try {
            // Get user
            $user = User::find(auth()->user()->id);

            // Get file from request
            $file = $request->file('file');

            // Get file model from database
            $fileModel = File::find($id);

            // Get file from storage
            $fileStorage = Storage::disk('uploads')->get($fileModel->uri);

            // If file's content from request is the same as file's content in storage
            if ($file->getContent() === $fileStorage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nothing to update'
                ]);
            }

            // Delete old file
            Storage::disk('uploads')->delete($fileModel->uri);

            // Decrease user's disk space in use
            $user->decrement('disk_space_used', $fileModel->size);

            // Update file model
            $fileModel->update([
                'size' => $file->getSize(),
            ]);

            // Increase user's disk space in use
            $user->increment('disk_space_used', $file->getSize());

            // Put new file in storage
            Storage::disk('uploads')->put(auth()->user()->id . '/' . $fileModel->current_dir, $file);


            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'File was updated',
            ], 200);
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
        if (!$userFilesOrigNames->contains($file->getClientOriginalName())) {
            return true;
        }

        return false;
    }
}
