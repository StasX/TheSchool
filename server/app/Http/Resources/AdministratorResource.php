<?php

namespace App\Http\Resources;

use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdministratorResource extends JsonResource
{
    /**
     * @var Administrator
     */
    public $resource;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->Administrator_ID,
            'email' => $this->Email,
            'name' => $this->Name,
            'role' => $this->Role,
            'phone' => $this->Phone,
            'image' => $this->Image,
        ];
    }
}
