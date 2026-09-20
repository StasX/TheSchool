<?php

namespace App\Http\Resources;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * @var Student
     */
    public $resource;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->Student_ID,
            'name' => $this->resource->Name,
            'email' => $this->resource->Email,
            'phone' => $this->resource->Phone,
            'image' => $this->resource->Image,
            'courses' => CourseResource::collection(
                $this->whenLoaded('courses')
            ),
        ];
    }
}
