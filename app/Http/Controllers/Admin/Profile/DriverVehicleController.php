<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverProfile;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Services\AudiLogsService;

class DriverVehicleController extends Controller
{
    public function index($driverProfileId)
    {
        $driverProfile = DriverProfile::findOrFail($driverProfileId);
        $vehicles = $driverProfile->vehicles()->with('vehicleType')->get();

        return view('admin.profile.vehicles.index', compact('driverProfile', 'vehicles'));
    }

    public function create($driverProfileId)
    {
        $driverProfile = DriverProfile::findOrFail($driverProfileId);
        $vehicleTypes = VehicleType::where('is_active', true)->get();  // Fetch vehicle types
        return view('admin.profile.vehicles.create', compact('driverProfile', 'vehicleTypes'));
    }

    public function store(Request $request, $driverProfileId)
    {
        $driverProfile = DriverProfile::findOrFail($driverProfileId);

        $validated = $request->validate([
            'make' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'year' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'color' => 'nullable|string|max:30',
            'license_plate' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('vehicles')->ignore($request->vehicle),
            ],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],  // updated here
            'seats' => 'nullable|integer|min:1|max:20',
            'image_url' => 'nullable|string', // handle upload separately
        ]);

        $vehicle = $driverProfile->vehicles()->create($validated);

        $new = $vehicle->toArray();
        AudiLogsService::storeLog('create', 'profile->vehicle', $vehicle->id, null, $new);
        return redirect()->route('admin.profile.vehicles.index', $driverProfileId)
            ->with('success', 'Vehicle added successfully.');
    }

    public function edit($driverProfileId, $vehicleId)
    {
        $driverProfile = DriverProfile::findOrFail($driverProfileId);
        $vehicle = $driverProfile->vehicles()->findOrFail($vehicleId);
        $vehicleTypes = VehicleType::where('is_active', true)->get();

        return view('admin.profile.vehicles.edit', compact('driverProfile', 'vehicle', 'vehicleTypes'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'color' => 'required|string|max:30',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,' . $vehicle->id,
            'vehicle_type_id' => 'required|exists:vehicle_types,id',  // updated here
            'seats' => 'required|integer|min:1|max:20',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $data = $request->only(['make', 'model', 'year', 'color', 'license_plate', 'vehicle_type_id', 'seats']);
        $old = $vehicle->toArray();

        if ($request->hasFile('image_url')) {
            if ($vehicle->image_url) {
                Storage::disk('public')->delete($vehicle->image_url);
            }
            $data['image_url'] = $request->file('image_url')->store('vehicles', 'public');
        }

        $vehicle->update($data);
        $new = $vehicle->toArray();
        AudiLogsService::storeLog('update', 'profile->vehicle', $vehicle->id, $old, $new);

        return redirect()->back()->with('success', 'Vehicle information updated successfully.');
    }

    public function destroy($driverProfileId, $vehicleId)
    {
        $driverProfile = DriverProfile::findOrFail($driverProfileId);
        $vehicle = $driverProfile->vehicles()->findOrFail($vehicleId);

        if ($vehicle->image_url) {
            Storage::disk('public')->delete($vehicle->image_url);
        }

        $old = $vehicle->toArray();
        $vehicle->delete();
        AudiLogsService::storeLog('delete', 'profile->vehicle', $vehicle->id, $old, null);
        return redirect()->route('admin.profile.vehicles.index', $driverProfileId)
            ->with('success', 'Vehicle deleted successfully.');
    }
}
