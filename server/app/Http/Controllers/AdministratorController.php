<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdministratorController extends Controller
{
    public function getAll(): JsonResponse
    {
        return response()->json(Administrator::query()
                ->select([
                    'Administrator_ID',
                    'Email',
                    'Name',
                    'Phone',
                    'Role',
                    'Image',
                ])
                ->get());
    }

    //------------------------------------------------------------------------

    public function getById(int $id): JsonResponse
    {
        $administrator = Administrator::query()
            ->select([
                'Administrator_ID',
                'Email',
                'Name',
                'Phone',
                'Role',
                'Image',
            ])
            ->find($id);

        if (! $administrator) {
            return response()->json([
                'error' => 'Administrator not found',
            ], 404);
        }

        return response()->json($administrator);
    }

    //------------------------------------------------------------------------

    public function add(Request $request): JsonResponse
    {
        /**
         * @var array{
         *     Email: string,
         *     Name: string,
         *     Role: 'manager'|'sales',
         *     Phone: string,
         *     Password: string,
         *     Image: UploadedFile
         * } $validated
         */
        $validated = $request->validate([
            'Email' => [
                'required',
                'email',
                'unique:administrators,Email',
                'max:64',
            ],
            'Name' => [
                'required',
                'string',
                'max:32',
            ],
            'Role' => [
                'required',
                Rule::in(['manager', 'sales']),
            ],
            'Phone' => [
                'required',
                'string',
                'max:16',
            ],
            'Password' => [
                'required',
                'string',
                'min:8',
            ],
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
        $data['Password'] = Hash::make($validated['Password']);
        $data['Image'] = "/upload/$filename";
        $administrator = Administrator::create($data);
        return response()->json($administrator, 201);
    }

    //------------------------------------------------------------------------

    public function update(Request $request, int $id): JsonResponse
    {
        /** @var Administrator $admin */
        $admin = Auth::user();
        $administrator = Administrator::find($id);
        if (! $administrator) {
            return response()->json([
                'error' => 'Administrator not found',
            ], 404);
        }

        if ($administrator->Role === 'owner' && $admin->Role !== 'owner') {
            return response()->json([
                'error' => 'Only an owner can modify an owner',
            ], 403);
        }
        /**
         * @var array{
         *     Email: string,
         *     Name: string,
         *     Role?: 'manager'|'owner'|'sales',
         *     Phone: string,
         *     Password?: string|null,
         *     Image?: UploadedFile|null
         * } $validated
         */
        $validated = $request->validate([
            'Email' => [
                'required',
                'email',
                'max:64',
                Rule::unique('administrators', 'Email')
                    ->ignore($id, 'Administrator_ID'),
            ],
            'Name' => [
                'required',
                'string',
                'max:32',
            ],
            'Role' => [
                'sometimes',
                Rule::in(['manager', 'owner', 'sales']),
            ],
            'Phone' => [
                'required',
                'string',
                'max:16',
            ],
            'Image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
            'Password' => [
                'sometimes',
                'nullable',
                'string',
                'min:8',
            ],
        ]);
        /** @var array<string, mixed> $data */
        $data = $validated;

        if ($administrator->Role === 'owner') {
            unset($data['Role']);
        } elseif (
            isset($data['Role']) &&
            $data['Role'] === 'owner'
        ) {
            return response()->json([
                'error' => 'Owner role cannot be assigned',
            ], 403);
        }

        if (! empty($validated['Password'])) {
            $data['Password'] = Hash::make($validated['Password']);
        } else {
            unset($data['Password']);
        }

        if ($request->hasFile('Image')) {
            $file = $request->file('Image');
            if (! $file instanceof UploadedFile) {
                return response()->json([
                    'error' => 'Invalid image',
                ], 422);
            }

            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            Storage::disk('uploads')->putFileAs('', $file, $filename);

            $oldImage = $administrator->Image;

            $data['Image'] = "/upload/$filename";

            if (
                $oldImage &&
                Storage::disk('uploads')->exists(basename($oldImage))
            ) {
                Storage::disk('uploads')->delete(basename($oldImage));
            }
        } else {
            unset($data['Image']);
        }

        $administrator->update($data);

        return response()->json($administrator);
    }

    //------------------------------------------------------------------------

    public function remove(int $id): JsonResponse
    {

        $administrator = Administrator::find($id);

        if (! $administrator) {
            return response()->json([
                'error' => 'Administrator not found',
            ], 404);
        }

        if ($administrator->Role === 'owner') {
            return response()->json([
                'error' => 'Owner cannot be removed',
            ], 403);
        }

        $oldImage = $administrator->Image;
        $administrator->delete();

        if ($oldImage && Storage::disk('uploads')->exists(basename($oldImage))) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }
        return response()->json(null, 204);
    }
}
