<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->Student_ID,
            'name'    => $this->Name,
            'email'   => $this->Email,
            'phone'   => $this->Phone,
            'image'   => $this->Image,
            'courses' => CourseResource::collection(
                $this->whenLoaded('courses')
            ),
        ];
    }
}
