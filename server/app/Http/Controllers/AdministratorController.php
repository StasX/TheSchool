<?php
namespace App\Http\Controllers;

use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdministratorController extends Controller
{
    public function getAll()
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return Administrator::query()
            ->select([
                'Administrator_ID',
                'Email',
                'Name',
                'Role',
            ])
            ->get();
    }

    public function getById(int $id)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $administrator = Administrator::find($id);

        if (! $administrator) {
            return response()->json([
                'error' => 'Administrator not found',
            ], 404);
        }

        return response()->json($administrator);
    }

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
            'Email'    => [
                'required',
                'email',
                'unique:administrators,Email',
            ],
            'Name'     => [
                'required',
                'string',
                'max:32',
            ],
            'Role'     => [
                'required',
                Rule::in(['manager', 'sales']),
            ],
            'Phone'    => [
                'required',
                'string',
                'max:16',
            ],
            'Password' => [
                'required',
                'string',
                'min:8',
            ],
            'Image'    => [
                'required',
                'string',
                'max:32',
            ],
        ]);

        $validated['Password'] = Hash::make(
            $validated['Password']
        );

        $administrator = Administrator::create($validated);

        return response()->json($administrator, 201);
    }

    public function update(Request $request, int $id)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $administrator = Administrator::find($id);

        if (! $administrator) {
            return response()->json([
                'error' => 'Administrator not found',
            ], 404);
        }

        // Only an owner can modify itself.
        if ($administrator->Role === 'owner' && $admin->Role !== 'owner') {
            return response()->json([
                'error' => 'Only an owner can modify an owner',
            ], 403);
        }

        $validated = $request->validate([
            'Email'    => [
                'sometimes',
                'required',
                'email',
            ],
            'Name'     => [
                'sometimes',
                'required',
                'string',
                'max:32',
            ],
            'Role'     => [
                'sometimes',
                'required',
                Rule::in(['manager', 'owner', 'sales']),
            ],
            'Phone'    => [
                'sometimes',
                'required',
                'string',
                'max:16',
            ],
            'Image'    => [
                'sometimes',
                'required',
                'string',
                'max:32',
            ],
            'Password' => [
                'sometimes',
                'required',
                'string',
                'min:8',
            ],
        ]);

        if (isset($validated['Password'])) {
            $validated['Password'] = Hash::make(
                $validated['Password']
            );
        }

        $administrator->update($validated);

        return response()->json($administrator);
    }

    public function remove(int $id)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $admin = Auth::user();

        if (! in_array($admin->Role, ['owner', 'manager'], true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

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

        $administrator->delete();

        return response()->json(null, 204);
    }
}
