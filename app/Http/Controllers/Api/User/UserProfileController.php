<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\Profile\User\UserUpdateProfileRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Shared\UserResource;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    public function show(User $user)
    {

    }
    public function update(UserUpdateProfileRequest $request)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $data = $request->validated();

            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imageName = (string) \Str::uuid() . '.' . $image->getClientOriginalExtension();
                $folder = $user->hasRole('driver') ? 'drivers' : 'users';
                $imagePath = $image->storeAs("public/{$folder}", $imageName);
                $data['avatar'] = 'storage/' . str_replace('public/', '', $imagePath);
            }

            $user->update($data);

            DB::commit();

            return response()->json([
                "message" => "Update is successful",
                "user" => new UserResource($user)
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy()
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $user->delete();

            DB::commit();

            return response()->json([
                'message' => 'Account deleted successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
