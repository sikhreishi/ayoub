<?php

namespace App\Http\Controllers\Admin\Drivers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Vehicle\VehicleType;
use App\Services\AudiLogsService;

class DriversController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.drivers.index');
    }

    public function unverifiedIndex()
    {
        return view('admin.drivers.unverified'); // You'll need to create this view
    }

    public function getDriversData(Request $request)
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })
            ->whereHas('driverProfile', function ($query) {
                $query->where('is_driver_verified', 1);
            })
            ->select('id', 'name', 'email', 'phone', 'avatar', 'created_at')
            ->get();

        return DataTables::of($users)
            ->addColumn('avatar', function ($row) {
                return $row->avatar
                    ? '<img src="' . asset('storage/' . $row->avatar) . '" alt="Avatar" width="50" height="50">'
                    : 'No Avatar';
            })
            ->addColumn('action', function ($row) {
                return '
                <button class="btn btn-sm btn-info edit-item" data-id="' . $row->id . '"
                        data-url="' . route('admin.drivers.update', $row->id) . '" data-table="#drivers-table">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <a href="' . route('admin.drivers.show', $row->id) . '" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> Profile
                </a>
                <button class="btn btn-sm btn-danger delete-item" data-id="' . $row->id . '"
                        data-url="' . route('admin.drivers.destroy', $row->id) . '" data-table="#drivers-table">
                    <i class="fas fa-trash"></i> Delete
                </button>
            ';
            })
            ->rawColumns(['avatar', 'action'])
            ->make(true);
    }
    public function getUnverifiedDrivers(Request $request)
    {
        $drivers = User::whereHas('roles', function ($query) {
            $query->where('name', 'driver');
        })
            ->whereHas('driverProfile', function ($query) {
                $query->where('is_driver_verified', 0);
            })
            ->select('id', 'name', 'email', 'phone', 'gender', 'avatar', 'created_at')
            ->get();

        return DataTables::of($drivers)
            ->addColumn('avatar', function ($row) {
                return $row->avatar ? '<img src="' . asset('storage/' . $row->avatar) . '" width="50" height="50">' : 'No Avatar';
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.drivers.show', $row->id) . '" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Profile
                    </a>
                    <button class="btn btn-sm btn-success verify-driver-btn"
                            data-id="' . $row->id . '"
                            data-url="' . route('admin.drivers.licenses', $row->id) . '"
                            data-bs-toggle="modal"
                            data-bs-target="#DriverInfoModal">
                        <i class="fas fa-check-circle"></i> Verify
                    </button>
                     <button class="btn btn-sm btn-danger delete-item" data-id="' . $row->id . '"
                            data-url="' . route('admin.drivers.destroy', $row->id) . '" data-table="#drivers-table">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                ';
            })
            ->rawColumns(['avatar', 'action'])
            ->make(true);
    }




    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'phone' => 'nullable|string|max:15',
                'language' => 'nullable|string|max:50',
                'password' => 'required|string|min:8|confirmed', // Ensure passwords match
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Optional avatar validation
            ]);

            // Create the new user
            $user = new User();

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->language = $request->input('language');
            $user->password = bcrypt($request->input('password'));

            // Handle avatar file upload (if provided)
            if ($request->hasFile('avatar')) {
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            $user->save();

            // Assign the 'driver' role to the new user
            $user->assignRole('driver'); // Ensure the role exists
            AudiLogsService::storeLog('create', 'users.driver', $user->id, null, $user->toArray());

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
        // Authorize viewing the profile


        $vehicleTypes = VehicleType::where('is_active', true)->get();

        return view('profile.edit', [
            'user' => $user,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }

    public function licenses($id)
    {
        try {
            $user = User::with('driverProfile')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load license info: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Driver license data not found.'
            ], 500);
        }
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

            Log::error('Error fetching driver data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch driver data.',
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
                'language' => 'nullable|string|max:50',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Optional avatar validation
            ]);

            // Find the driver by ID
            $user = User::findOrFail($id);
            $old = $user->toArray();

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->language = $request->input('language');

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

            AudiLogsService::storeLog('update', 'users.driver', $user->id, $old, $new);

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
            $driver = User::findOrFail($id);
            $old = $driver->toArray();

            $driver->delete();
            AudiLogsService::storeLog('delete', 'users', $id, $old, null);

            return response()->json([
                'success' => true,
                'message' => 'Driver deleted successfully!',
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting driver: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
