<?php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function getAll()
    {
        return Course::with('students')->get();
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
        $validated = $request->validate([
            'Name'        => ['required', 'string', 'max:32'],
            'Description' => ['required', 'string', 'max:500'],
            'Image'       => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
        ]);
        $file     = $request->file('Image');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('uploads')->putFileAs('', $file, $filename);
        $validated['Image'] = "/upload/$filename";
        $course             = Course::create($validated);

        return response()->json($course, 201);
    }

//------------------------------------------------------------------------

    public function update(Request $request, int $id): JsonResponse
    {
        $course = Course::find($id);

        if (! $course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $validated = $request->validate([
            'Name'        => ['required', 'string', 'max:32'],
            'Description' => ['required', 'string', 'max:500'],
            'Image'       => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
        ]);
        if ($request->hasFile('Image')) {
            $file = $request->file('Image');

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            Storage::disk('uploads')->putFileAs('', $file, $filename);

            $oldImage = $course->Image;

            $validated['Image'] = "/upload/$filename";

            if (
                $oldImage &&
                Storage::disk('uploads')->exists(basename($oldImage))
            ) {
                Storage::disk('uploads')->delete(basename($oldImage));
            }
        }

        $course->update($validated);

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
