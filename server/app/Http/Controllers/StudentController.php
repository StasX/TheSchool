<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function getAll(): JsonResponse
    {
        return response()->json(
            Student::with('courses')->get()
        );
    }

//------------------------------------------------------------------------

    public function getById(int $id): JsonResponse
    {
        $student = Student::with('courses')->find($id);

        if (! $student) {
            return response()->json([
                'error' => 'Student not found',
            ], 404);
        }

        return response()->json($student);
    }

//------------------------------------------------------------------------

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'Email'     => [
                'required',
                'email',
                'unique:students,Email',
            ],
            'Name'      => [
                'required',
                'string',
                'max:32',
            ],
            'Phone'     => [
                'required',
                'string',
                'max:54',
            ],
            'Image'     => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif',
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
        $file     = $request->file('Image');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('uploads')->putFileAs('', $file, $filename);
        $validated['Image'] = "/upload/$filename";
        $courses            = $validated['courses'] ?? [];
        unset($validated['courses']);
        $student = Student::create($validated);
        $student->courses()->sync($courses);
        return response()->json($student->load('courses'), 201);
    }

//------------------------------------------------------------------------

    public function update(Request $request, int $id): JsonResponse
    {
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
                'max:60',
            ],
            'Name'      => [
                'required',
                'string',
                'max:32',
            ],
            'Phone'     => [
                'required',
                'string',
                'max:54',
            ],
            'Image'     => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif',
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

        $oldImage = $student->Image;

        $student->update(
            collect($validated)
                ->except('courses')
                ->toArray()
        );

        if ($request->hasFile('Image') && $oldImage && Storage::disk('uploads')->exists(basename($oldImage))) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }

        if (array_key_exists('courses', $validated)) {
            $student->courses()->sync($validated['courses']);
        }
        $student->refresh();
        return response()->json(
            $student->load('courses')
        );
    }

//------------------------------------------------------------------------

    public function remove(int $id): JsonResponse
    {
        $student = Student::find($id);

        if (! $student) {
            return response()->json([
                'error' => 'Student not found',
            ], 404);
        }
        $oldImage = $student->Image;
        $student->courses()->detach();
        $student->delete();

        if ($oldImage && Storage::disk('uploads')->exists(basename($oldImage))) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }

        return response()->json(null, 204);
    }
}
