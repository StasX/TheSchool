<?php

namespace App\Http\Resources;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * @var Course
     */
    public $resource;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->Course_ID,
            'name' => $this->resource->Name,
            'description' => $this->resource->Description,
            'image' => $this->resource->Image,
            'students' => StudentResource::collection(
                $this->whenLoaded('students')
            ),
        ];
    }
}
