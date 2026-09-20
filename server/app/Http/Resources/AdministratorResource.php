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
            'id' => $this->resource->Administrator_ID,
            'email' => $this->resource->Email,
            'name' => $this->resource->Name,
            'role' => $this->resource->Role,
            'phone' => $this->resource->Phone,
            'image' => $this->resource->Image,
        ];
    }
}
