<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function getAll(): JsonResponse
    {
        return StudentResource::collection(
            Student::with('courses')->get()
        )->response();
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

        return (new StudentResource($student))->response();
    }

    //------------------------------------------------------------------------

    public function add(Request $request): JsonResponse
    {
        /**
         * @var array{
         *     email: string,
         *     name: string,
         *     phone: string,
         *     image: UploadedFile,
         *     courses?: array<int, int>
         * } $validated
         */
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'unique:students,Email',
            ],
            'name' => [
                'required',
                'string',
                'max:32',
            ],
            'phone' => [
                'required',
                'string',
                'max:54',
            ],
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
            'courses' => [
                'sometimes',
                'array',
            ],
            'courses.*' => [
                'integer',
                'distinct',
                'exists:courses,Course_ID',
            ],
        ]);
        $file = $request->file('image');
        if (! $file instanceof UploadedFile) {
            return response()->json([
                'error' => 'Invalid image',
            ], 422);
        }
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('uploads')->putFileAs('', $file, $filename);
        /** @var array<string, mixed> $data */
        $data = [
            'Email' => $validated['email'],
            'Name' => $validated['name'],
            'Phone' => $validated['phone'],
            'Image' => "/upload/$filename"
            ];
        $courses = $validated['courses'] ?? [];
        $student = Student::create($data);
        $student->courses()->sync($courses);
        return (new StudentResource($student->load('courses')))
        ->response()
        ->setStatusCode(201);
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
         *     email: string,
         *     name: string,
         *     phone: string,
         *     image?: UploadedFile|null,
         *     courses?: array<int, int>
         * } $validated
         */
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('students', 'Email')
                    ->ignore($id, 'Student_ID'),
                'max:60',
            ],
            'name' => [
                'required',
                'string',
                'max:32',
            ],
            'phone' => [
                'required',
                'string',
                'max:54',
            ],
            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
            'courses' => [
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
        $data = [
            'Email' => $validated['email'],
            'Name' => $validated['name'],
            'Phone' => $validated['phone']
        ];

        $courses = $validated['courses'] ?? null;

        $oldImage = $student->Image;
        $imageChanged = $request->hasFile('image');
        if ($imageChanged) {
            $file = $request->file('image');

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
        }

        $student->update($data);

        if (
            $imageChanged &&
            $oldImage &&
            Storage::disk('uploads')->exists(basename($oldImage))
        ) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }

        if ($courses !== null) {
            $student->courses()->sync($courses);
        }

        $student->refresh();
        return (new StudentResource($student->load('courses')))
        ->response();
    }

    //------------------------------------------------------------------------

    public function remove(int $id): Response
    {
        $student = Student::find($id);

        if (! $student) {
            return response('', Response::HTTP_NOT_FOUND);
        }
        $oldImage = $student->Image;
        $student->courses()->detach();
        $student->delete();

        if ($oldImage && Storage::disk('uploads')->exists(basename($oldImage))) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }

        return response('', Response::HTTP_NO_CONTENT);
    }
}
