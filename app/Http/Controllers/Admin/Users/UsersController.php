<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Laratrust\Models\Role;
use Illuminate\Support\Facades\Storage;
use App\Models\Vehicle\VehicleType;
use App\Services\AudiLogsService;
use App\Models\Wallet;

class UsersController extends Controller
{

    public function index(Request $request)
    {
        $countries = \App\Models\Country::all();
        return view('admin.users.index', compact('countries'));
    }

    public function getUsersData(Request $request)
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'user'); // Replace 'user' with the actual role name
        })
            ->select('id', 'name', 'email', 'phone', 'language', 'gender', 'avatar', 'created_at')
            ->get();

        return DataTables::of($users)
            ->addColumn('avatar', function ($row) {
                return $row->avatar ? '<img src="' . asset('storage/' . $row->avatar) . '" alt="Avatar" width="50" height="50">' : 'No Avatar';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-info edit-item" data-id="' . $row->id . '"
                                data-url="' . route('admin.users.update', $row->id) . '" data-table="#users-table">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                         <a href="' . route('admin.drivers.show', $row->id) . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Profile
                        </a>
                        <button class="btn btn-sm btn-danger delete-item" data-id="' . $row->id . '" data-url="' . route('admin.users.destroy', $row->id) . '"
                            data-table="#users-table">
                                <i class="fas fa-trash"></i> Delete
                        </button>';
            })
            ->rawColumns(['avatar', 'action'])
            ->make(true);
    }


    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'phone' => 'nullable|string|max:15',
                'password' => 'required|string|min:8|confirmed',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'country_id' => 'nullable|exists:countries,id',
            ]);

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->password = bcrypt($request->input('password'));
            $user->country_id = $request->input('country_id');

            if ($request->hasFile('avatar')) {
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

           
            

            $user->save(); 
            
            $user->wallet()->save(new Wallet(['balance' => 0.00]));

            $new = $user->toArray();
            AudiLogsService::storeLog('create', 'users', $user->id, null, $new);

            $user->assignRole('user');

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Create failed: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        $vehicleTypes = VehicleType::where('is_active', true)->get();

        return view('profile.edit', [
            'user' => $user,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {

            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user,
            ]);
        } catch (\Exception $e) {

            Log::error('Error fetching user data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user data.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:15',
                'country_id' => 'nullable|exists:countries,id',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Optional avatar validation
            ]);

            // Find the user by ID
            $user = User::findOrFail($id);
            $old = $user->toArray();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->country_id = $request->input('country_id');

            // Handle avatar file upload (if provided)
            if ($request->hasFile('avatar')) {
                // Remove old avatar if it exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                // Store the new avatar
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            $user->save();
            $new = $user->fresh()->toArray();
            AudiLogsService::storeLog('update', 'users', $user->id, $old, $new);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $old = $user->toArray();
            $user->delete();
            AudiLogsService::storeLog('delete', 'users', $id, $old, null);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!',
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
