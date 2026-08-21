<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
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

        return response()->json(
            Student::with('courses')->get()
        );
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

        $student = Student::with('courses')->find($id);

        if (! $student) {
            return response()->json([
                'error' => 'Student not found',
            ], 404);
        }

        return response()->json($student);
    }

//------------------------------------------------------------------------

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
            'Email' => [
                'required',
                'email',
                'unique:students,Email',
            ],
            'Name'  => [
                'required',
                'string',
                'max:32',
            ],
            'Phone' => [
                'required',
                'string',
                'max:54',
            ],
            'Image' => [
                'required',
                'string',
                'max:32',
            ],
        ]);

        $student = Student::create($validated);

        return response()->json($student, 201);
    }

//------------------------------------------------------------------------

    public function update(Request $request, int $id)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager', 'sales'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $student = Student::find($id);

        if (! $student) {
            return response()->json([
                'error' => 'Student not found',
            ], 404);
        }

        $validated = $request->validate([
            'Email'     => [
                'sometimes',
                'required',
                'email',
                Rule::unique('students', 'Email')
                    ->ignore($id, 'Student_ID'),
            ],
            'Name'      => [
                'sometimes',
                'required',
                'string',
                'max:32',
            ],
            'Phone'     => [
                'sometimes',
                'required',
                'string',
                'max:54',
            ],
            'Image'     => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'courses'   => [
                'sometimes',
                'array',
            ],
            'courses.*' => [
                'integer',
                'distinct',
                'exists:courses,Course_ID',
            ],
        ]);
        if ($request->hasFile('Image')) {
            $file = $request->file('Image');

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            Storage::disk('uploads')->putFileAs(
                '',
                $file,
                $filename
            );

            $validated['Image'] = "/upload/$filename";
        }

        $student->update(
            collect($validated)
                ->except('courses')
                ->toArray()
        );

        if (array_key_exists('courses', $validated)) {
            $student->courses()->sync($validated['courses']);
        }
        $student->refresh();
        return response()->json(
            $student->load('courses')
        );
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

        $student = Student::find($id);

        if (! $student) {
            return response()->json([
                'error' => 'Student not found',
            ], 404);
        }

        $student->courses()->detach();
        $student->delete();

        return response()->json(null, 204);
    }
}
