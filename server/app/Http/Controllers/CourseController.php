<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function getAll(): JsonResponse
    {
        return CourseResource::collection(
            Course::with('students')->get()
        )->response();
    }

    //------------------------------------------------------------------------

    public function getById(int $id): JsonResponse
    {
        $course = Course::with('students')->find($id);

        if (! $course) {
            return response()->json([
                'error' => 'Course not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return (new CourseResource($course))->response();
    }

    //------------------------------------------------------------------------

    public function add(Request $request): JsonResponse
    {
        /**
         * @var array{
         *     name: string,
         *     description: string,
         *     image: UploadedFile
         * } $validated
         */
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:32'],
            'description' => ['required', 'string', 'max:500'],
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
        ]);
        $file = $request->file('image');
        if (! $file instanceof UploadedFile) {
            return response()->json([
                'error' => 'Invalid image',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('uploads')->putFileAs('', $file, $filename);
        /** @var array<string, mixed> $data */
        $data = [
            'Name' => $validated['name'],
            'Description' => $validated['description'],
            'Image' => "/upload/$filename",
        ];
        $course = Course::create($data);
        return (new CourseResource($course))
        ->response()
        ->setStatusCode(Response::HTTP_CREATED);
    }

    //------------------------------------------------------------------------

    public function update(Request $request, int $id): JsonResponse
    {
        $course = Course::find($id);

        if (! $course) {
            return response()->json(['error' => 'Course not found'], Response::HTTP_NOT_FOUND);
        }
        /**
         * @var array{
         *     name: string,
         *     description: string,
         *     image?: UploadedFile|null
         * } $validated
         */
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:32'],
            'description' => ['required', 'string', 'max:500'],
            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
        ]);
        /** @var array<string, mixed> $data */
        $data = [
            'Name' => $validated['name'],
            'Description' => $validated['description'],
        ];
        $oldImage = $course->Image;
        $imageChanged = $request->hasFile('image');

        if ($imageChanged) {
            $file = $request->file('image');

            if (! $file instanceof UploadedFile) {
                return response()->json([
                    'error' => 'Invalid image',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            Storage::disk('uploads')->putFileAs(
                '',
                $file,
                $filename
            );

            $data['Image'] = "/upload/$filename";
        }

        $course->update($data);

        if (
            $imageChanged &&
            $oldImage &&
            Storage::disk('uploads')->exists(basename($oldImage))
        ) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }

        return (new CourseResource(
            $course->refresh()->load('students')
        ))->response();
    }

    //------------------------------------------------------------------------

    public function remove(int $id): Response
    {
        $course = Course::find($id);

        if (! $course) {
            return response('', Response::HTTP_NOT_FOUND);
        }
        $oldImage = $course->Image;
        $course->students()->detach();
        $course->delete();
        if ($oldImage && Storage::disk('uploads')->exists(basename($oldImage))) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }

        return response('', Response::HTTP_NO_CONTENT);
    }
}
