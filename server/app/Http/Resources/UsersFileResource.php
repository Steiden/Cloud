<?php

namespace App\Http\Resources;

use App\Models\AccessType;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersFileResource extends JsonResource
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
            'user' => new UserResource(User::find($this->user_id)),
            'file' => new FileResource(File::find($this->file_id)),
            'access_type' => new AccessTypeResource(AccessType::find($this->access_type_id))
        ];
    }
}
