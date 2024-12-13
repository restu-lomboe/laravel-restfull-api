<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        \DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->profile()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json(
                [
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }

        return response()->json(
            [
                'message' => 'user created successfully',
                'user' => $user,
            ],
            200,
        );
    }

    public function list(Request $request)
    {
        $request->validate([
            /** @query */
            'page' => ['required', 'integer'],
            'perPage' => ['required', 'integer'],
        ]);

        $page = $request->page ?? 1;
        $perPage = $request->perPage ?? 10;

        $offset = ($page - 1) * $perPage;

        $users = User::with('profile')->paginate($perPage, ['*'], 'page', $page);

        return response()->json(
            [
                'message' => 'get user list successfully',
                'users' => $users,
            ],
            200,
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        \DB::beginTransaction();

        try {
            $user = User::where('id', $id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user_profile = UserProfile::where('user_id', $id)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json(
                [
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }

        return response()->json(
            [
                'message' => 'user created successfully',
                'user' => $user,
            ],
            200,
        );
    }

    public function delete($id)
    {
        $user = User::find($id)->delete();

        return response()->json(
            [
                'message' => 'user deleted successfully',
            ],
            200,
        );
    }
}
