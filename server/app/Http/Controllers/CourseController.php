<?php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function add(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager', 'sales'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'Name'        => ['required', 'string', 'max:255'],
            'Description' => ['nullable', 'string'],
            'Image'       => ['required', 'string', 'max:255'],
        ]);

        $course = Course::create($validated);

        return response()->json($course, 201);
    }

    public function update(Request $request, int $id)
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

        $validated = $request->validate([
            'Name'        => ['sometimes', 'required', 'string', 'max:255'],
            'Description' => ['sometimes', 'nullable', 'string'],
            'Image'       => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $course->update($validated);

        return response()->json($course);
    }

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
