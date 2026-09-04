<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function getAll(): JsonResponse
    {
        return response()->json(Course::with('students')->get());
    }

    //------------------------------------------------------------------------

    public function getById(int $id): JsonResponse
    {
        $course = Course::with('students')->find($id);

        if (! $course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        return response()->json($course);
    }

    //------------------------------------------------------------------------

    public function add(Request $request): JsonResponse
    {
        /**
         * @var array{
         *     Name: string,
         *     Description: string,
         *     Image: UploadedFile
         * } $validated
         */
        $validated = $request->validate([
            'Name' => ['required', 'string', 'max:32'],
            'Description' => ['required', 'string', 'max:500'],
            'Image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
        ]);
        $file = $request->file('Image');
        if (! $file instanceof UploadedFile) {
            return response()->json([
                'error' => 'Invalid image',
            ], 422);
        }
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('uploads')->putFileAs('', $file, $filename);
        /** @var array<string, mixed> $data */
        $data = $validated;
        $data['Image'] = "/upload/$filename";
        $course = Course::create($data);

        return response()->json($course, 201);
    }

    //------------------------------------------------------------------------

    public function update(Request $request, int $id): JsonResponse
    {
        $course = Course::find($id);

        if (! $course) {
            return response()->json(['error' => 'Course not found'], 404);
        }
        /**
         * @var array{
         *     Name: string,
         *     Description: string,
         *     Image?: UploadedFile|null
         * } $validated
         */
        $validated = $request->validate([
            'Name' => ['required', 'string', 'max:32'],
            'Description' => ['required', 'string', 'max:500'],
            'Image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
        ]);
        /** @var array<string, mixed> $data */
        $data = $validated;
        if ($request->hasFile('Image')) {
            $file = $request->file('Image');
            if (! $file instanceof UploadedFile) {
                return response()->json([
                    'error' => 'Invalid image',
                ], 422);
            }
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            Storage::disk('uploads')->putFileAs('', $file, $filename);

            $oldImage = $course->Image;

            $data['Image'] = "/upload/$filename";

            if (
                $oldImage &&
                Storage::disk('uploads')->exists(basename($oldImage))
            ) {
                Storage::disk('uploads')->delete(basename($oldImage));
            }
        }

        $course->update($data);

        return response()->json($course);
    }

    //------------------------------------------------------------------------

    public function remove(int $id): JsonResponse
    {
        $course = Course::find($id);

        if (! $course) {
            return response()->json(['error' => 'Course not found'], 404);
        }
        $oldImage = $course->Image;
        $course->students()->detach();
        $course->delete();
        if ($oldImage && Storage::disk('uploads')->exists(basename($oldImage))) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }

        return response()->json(null, 204);
    }
}
