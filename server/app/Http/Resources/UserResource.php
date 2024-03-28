<?php

namespace App\Http\Resources;

use App\Models\File;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'login' => $this->login,
            'email' => $this->email,
            'disk_space' => $this->disk_space,
            'disk_space_used' => $this->disk_space_used,
            'avatar' => new FileResource(File::find($this->avatar)),
            'role' => new RoleResource(Role::find($this->role_id)),
        ];
    }
}
