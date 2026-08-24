<?php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function getAll()
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager', 'sales'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return Course::with('students')->get();
    }

//------------------------------------------------------------------------

    public function getById(int $id)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager', 'sales'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $course = Course::with('students')->find($id);

        if (! $course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        return $course;
    }

//------------------------------------------------------------------------

    public function add(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'Name'        => ['required', 'string', 'max:255'],
            'Description' => ['required', 'string'],
            'Image'       => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
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

    public function update(Request $request, int $id)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $course = Course::find($id);

        if (! $course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $validated = $request->validate([
            'Name'        => ['required', 'string', 'max:255'],
            'Description' => ['required', 'string', 'max:255'],
            'Image'       => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
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

    public function remove(int $id)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager', 'sales'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $course = Course::find($id);

        if (! $course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $course->students()->detach();
        $course->delete();

        return response()->json(null, 204);
    }
}
