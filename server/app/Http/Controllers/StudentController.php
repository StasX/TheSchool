<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
        /**
         * @var array{
         *     Email: string,
         *     Name: string,
         *     Phone: string,
         *     Image: UploadedFile,
         *     courses?: array<int, int>
         * } $validated
         */
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
        /** @var array<string, mixed> $data */
        $data = $validated;
        $file = $request->file('Image');
        if (! $file instanceof UploadedFile) {
            return response()->json([
                'error' => 'Invalid image',
            ], 422);
        }
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('uploads')->putFileAs('', $file, $filename);
        $data['Image'] = "/upload/$filename";
        $courses       = $validated['courses'] ?? [];
        unset($data['courses']);
        $student = Student::create($data);
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
        /**
         * @var array{
         *     Email: string,
         *     Name: string,
         *     Phone: string,
         *     Image?: UploadedFile|null,
         *     courses?: array<int, int>
         * } $validated
         */
        $validated = $request->validate([
            'Email'     => [
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
        /** @var array<string, mixed> $data */
        $data = $validated;

        $courses = $validated['courses'] ?? null;

        unset($data['courses']);

        $oldImage = $student->Image;

        if ($request->hasFile('Image')) {
            $file = $request->file('Image');

            if (! $file instanceof UploadedFile) {
                return response()->json([
                    'error' => 'Invalid image',
                ], 422);
            }

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            Storage::disk('uploads')->putFileAs(
                '',
                $file,
                $filename
            );

            $data['Image'] = "/upload/$filename";
        } else {
            unset($data['Image']);
        }

        $student->update($data);

        if (
            $request->hasFile('Image') &&
            $oldImage &&
            Storage::disk('uploads')->exists(basename($oldImage))
        ) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }

        if ($courses !== null) {
            $student->courses()->sync($courses);
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
