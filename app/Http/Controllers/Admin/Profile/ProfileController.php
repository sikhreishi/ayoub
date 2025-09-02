<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\Address;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Vehicle\VehicleType;
use App\Services\AudiLogsService;

class ProfileController extends Controller
{

    public function show($id)
    {
        $user = User::with('addresses.city')->findOrFail($id);

        // Authorize viewing the profile

        if ($user->hasRole('driver')) {

            $user->load('driverProfile');
        }
        $vehicleTypes = VehicleType::where('is_active', true)->get();

        return view('profile.edit', [
            'user' => $user,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }


    public function update(ProfileUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);

        // Authorize updating the user

        $old = $user->toArray();
        $validated = $request->validated();
        $user->update($validated);
        $new = $user->toArray();
        AudiLogsService::storeLog('update', 'profile->user', $user->id, $old, $new);
        return Redirect::route('profile.show', $user->id)->with('status', 'profile-updated');
    }

    public function updateExtra(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $old = $user->toArray();
        // Authorize updating the user

        $validated = $request->validate([
            'language' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'current_lat' => ['nullable', 'numeric'],
            'current_lng' => ['nullable', 'numeric'],
            'geohash' => ['nullable', 'string'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'current_address_id' => ['nullable', 'exists:addresses,id'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        if ($validated['current_address_id'] && !$user->addresses->contains('id', $validated['current_address_id'])) {
            return redirect()->back()->withErrors(['current_address_id' => 'Invalid address selected.']);
        }

        $user->update($validated);
        $new = $user->toArray();
        AudiLogsService::storeLog('update', 'profile->update-extra', $user->id, $old, $new);
        return redirect()->back()->with('status', 'Extra profile info updated!');
    }



    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Authorize deleting the user

        $old = $user->toArray();
        $user->delete();
        AudiLogsService::storeLog('delete', 'profile->user', $user->id, $old, null);
        if (Auth::user()->hasRole('admin')) {
            if ($user->hasRole('driver')) {
                return Redirect::to('/dashboard/admin/drivers');
            } else {
                return Redirect::to('/dashboard/admin/users');
            }
        } else {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return Redirect::to('/');
        }
    }
}
