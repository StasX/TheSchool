<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdministratorResource;
use App\Models\Administrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdministratorController extends Controller
{
    public function getAll(): JsonResponse
    {
        /** @var Administrator $admin */
        $admin = Auth::user();

        return AdministratorResource::collection(
            Administrator::visibleTo($admin)->get()
        )->response();
    }

    //------------------------------------------------------------------------

    public function getById(string $id): JsonResponse
    {
        if (! ctype_digit($id)) {
            return response()->json([
                'error' => 'Administrator not found',
            ], Response::HTTP_NOT_FOUND);
        }

        /** @var Administrator $admin */
        $admin = Auth::user();

        $administrator = Administrator::visibleTo($admin)
            ->where('Administrator_ID', (int) $id)
            ->first();

        if (! $administrator) {
            return response()->json([
                'error' => 'Administrator not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return (new AdministratorResource($administrator))->response();
    }

    //------------------------------------------------------------------------

    public function add(Request $request): JsonResponse
    {
        /**
         * @var array{
         *     email: string,
         *     name: string,
         *     role: 'manager'|'sales',
         *     phone: string,
         *     password: string,
         *     image: UploadedFile
         * } $validated
         */
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'unique:administrators,Email',
                'max:64',
            ],
            'name' => [
                'required',
                'string',
                'max:32',
            ],
            'role' => [
                'required',
                Rule::in(['manager', 'sales']),
            ],
            'phone' => [
                'required',
                'string',
                'max:16',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
            ],
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
            'Email' => $validated['email'],
            'Name' => $validated['name'],
            'Role' => $validated['role'],
            'Phone' => $validated['phone'],
            'Password' => Hash::make($validated['password']),
            'Image' => "/upload/$filename",
        ];
        $administrator = Administrator::create($data);
        return (new AdministratorResource($administrator))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
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
            ], Response::HTTP_NOT_FOUND);
        }

        if ($administrator->Role === 'owner' && $admin->Role !== 'owner') {
            return response()->json([
                'error' => 'Only an owner can modify an owner',
            ], Response::HTTP_FORBIDDEN);
        }
        /**
         * @var array{
         *     email: string,
         *     name: string,
         *     role?: 'manager'|'owner'|'sales',
         *     phone: string,
         *     password?: string|null,
         *     image?: UploadedFile|null
         * } $validated
         */
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:64',
                Rule::unique('administrators', 'Email')
                    ->ignore($id, 'Administrator_ID'),
            ],
            'name' => [
                'required',
                'string',
                'max:32',
            ],
            'role' => [
                'sometimes',
                Rule::in(['manager', 'owner', 'sales']),
            ],
            'phone' => [
                'required',
                'string',
                'max:16',
            ],
            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
            'password' => [
                'sometimes',
                'nullable',
                'string',
                'min:8',
            ],
        ]);
        /** @var array<string, mixed> $data */
        $data = [
            'Email' => $validated['email'],
            'Name' => $validated['name'],
            'Role' => $validated['role'] ?? $administrator->Role,
            'Phone' => $validated['phone'],
        ];

        if ($administrator->Role === 'owner') {
            unset($data['Role']);
        } elseif (
            isset($data['Role']) &&
            $data['Role'] === 'owner'
        ) {
            return response()->json([
                'error' => 'Owner role cannot be assigned',
            ], Response::HTTP_FORBIDDEN);
        }

        if (! empty($validated['password'])) {
            $data['Password'] = Hash::make($validated['password']);
        }

        $oldImage = $administrator->Image;
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

        $administrator->update($data);
        if (
            $imageChanged &&
            $oldImage &&
            Storage::disk('uploads')->exists(basename($oldImage))
        ) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }
        return (new AdministratorResource($administrator))->response();
    }

    //------------------------------------------------------------------------

    public function remove(int $id): Response
    {

        $administrator = Administrator::find($id);

        if (! $administrator) {
            return response('', Response::HTTP_NOT_FOUND);
        }

        if ($administrator->Role === 'owner') {
            return response('', Response::HTTP_FORBIDDEN);
        }

        $oldImage = $administrator->Image;
        $administrator->delete();

        if ($oldImage && Storage::disk('uploads')->exists(basename($oldImage))) {
            Storage::disk('uploads')->delete(basename($oldImage));
        }
        return response('', Response::HTTP_NO_CONTENT);
    }

    //------------------------------------------------------------------------

    public function getCount(): JsonResponse
    {
        return response()->json([
            'count' => Administrator::count(),
        ]);
    }
}
