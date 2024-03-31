<?php

namespace App\Http\Resources;

use App\Models\FileType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'original_name' => $this->original_name,
            'uri' => $this->uri,
            'current_dir' => $this->current_dir,
            'size' => $this->size,
            'file_type' => new FileTypeResource(FileType::find($this->file_type_id)),
            'owner' => new UserResource(User::find($this->owner)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
