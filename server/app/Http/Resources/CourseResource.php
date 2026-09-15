<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->Course_ID,
            'name'        => $this->Name,
            'description' => $this->Description,
            'image'       => $this->Image,
            'students'    => StudentResource::collection(
                $this->whenLoaded('students')
            ),
        ];
    }
}
