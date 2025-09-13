<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\Profile\User\UserUpdateProfileRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Shared\UserResource;
use App\Http\Requests\Api\Profile\User\UserChangePasswordRequest;
use Illuminate\Support\Facades\Hash;


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
        // Step 1: Save Avatar if provided
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $imageName = (string) \Str::uuid() . '.' . $image->getClientOriginalExtension();
            
            // Set the folder path for avatars
            $folder = 'avatars';

            // Create the avatars folder if it does not exist
            $storagePath = storage_path("app/public/{$folder}");
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0777, true); // Create the folder with full permissions
            }

            // Store the image in the avatars folder
            $imagePath = $image->storeAs("public/{$folder}", $imageName);
            $avatarPath = 'storage/' . str_replace('public/', '', $imagePath);
        }

        // Step 2: Save other user details (name, email, etc.)
        $data = $request->validated();
        
        // If avatar is updated, include the avatar path
        if ($avatarPath) {
            $data['avatar'] = $avatarPath;
        }

        // Update the user with the validated data
        $user->update($data);

        DB::commit();

        return response()->json([
            "message" => "Update is successful",
            "user" => new UserResource($user),
            "avatar" => $avatarPath, // Optionally include the updated avatar URL
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

    public function updatePassword(UserChangePasswordRequest $request)
{
    $user = Auth::user();

    DB::beginTransaction();
    try {
        $user->password = Hash::make($request->input('password'));
        $user->save();

        DB::commit();

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Something went wrong',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

}
