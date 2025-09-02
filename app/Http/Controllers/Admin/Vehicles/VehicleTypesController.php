<?php

namespace App\Http\Controllers\Admin\Vehicles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle\VehicleType;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\AudiLogsService;

class VehicleTypesController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.vehicles.types.index');
    }

    public function getVehicleTypesData(Request $request)
    {
        $vehicleTypes = VehicleType::select(
            'id',
            'name',
            'description',
            'start_fare',
            'day_per_km_rate',
            'night_per_km_rate',
            'day_per_minute_rate',
            'night_per_minute_rate',
            'commission_percentage',
            'is_active',
            'icon_url',
            'created_at'
        )
            ->get();

        return DataTables::of($vehicleTypes)
            ->addColumn('status', fn($row) => $row->is_active ? 'Active' : 'Inactive')
            ->addColumn('icon', fn($row) => $row->icon_url ? '<img src="' . asset($row->icon_url) . '" height="32">' : 'N/A')
            ->addColumn('commission', fn($row) => $row->commission_percentage . '%')
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-info edit-item" data-id="' . $row->id . '" data-url="' . route('admin.vehicle_types.update', $row->id) . '" data-table="#vehicle-types-table">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger delete-item" data-id="' . $row->id . '" data-url="' . route('admin.vehicle_types.destroy', $row->id) . '" data-table="#vehicle-types-table">
                    <i class="fas fa-trash"></i> Delete
                </button>';
            })
            ->rawColumns(['action', 'icon'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.vehicles.types.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_fare' => 'required|numeric',
                'day_per_km_rate' => 'required|numeric',
                'night_per_km_rate' => 'required|numeric',
                'day_per_minute_rate' => 'required|numeric',
                'night_per_minute_rate' => 'required|numeric',
                'is_active' => 'nullable|boolean',
                'icon_url' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);

            $iconUrl = null;
            if ($request->hasFile('icon_url')) {
                $iconUrl = $request->file('icon_url')->store('vehicle_types', 'public');
            }

            $globalCommission = VehicleType::value('commission_percentage') ?? 10.00;

            $vehicleType = new VehicleType();
            $vehicleType->name = $request->input('name');
            $vehicleType->description = $request->input('description');
            $vehicleType->start_fare = $request->input('start_fare');
            $vehicleType->day_per_km_rate = $request->input('day_per_km_rate');
            $vehicleType->night_per_km_rate = $request->input('night_per_km_rate');
            $vehicleType->day_per_minute_rate = $request->input('day_per_minute_rate');
            $vehicleType->night_per_minute_rate = $request->input('night_per_minute_rate');
            $vehicleType->commission_percentage = $globalCommission;
            $vehicleType->is_active = $request->input('is_active', true);
            $vehicleType->icon_url = $iconUrl;
            $vehicleType->save();

            $new = $vehicleType->toArray();
            AudiLogsService::storeLog('create', 'vehicleType', $vehicleType->id, null, $new);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle type created successfully!',
                'data' => $vehicleType,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating vehicle type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Create failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $vehicleType = VehicleType::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $vehicleType,
        ]);
    }

    public function edit($id)
    {
        try {
            $vehicleType = VehicleType::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $vehicleType,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching vehicle type data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch vehicle type data.',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_fare' => 'required|numeric',
                'day_per_km_rate' => 'required|numeric',
                'night_per_km_rate' => 'required|numeric',
                'day_per_minute_rate' => 'required|numeric',
                'night_per_minute_rate' => 'required|numeric',
                'is_active' => 'nullable|boolean',
                'icon_url' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);

            $vehicleType = VehicleType::findOrFail($id);
            $old = $vehicleType->toArray();

            $vehicleType->name = $request->input('name');
            $vehicleType->description = $request->input('description');
            $vehicleType->start_fare = $request->input('start_fare');
            $vehicleType->day_per_km_rate = $request->input('day_per_km_rate');
            $vehicleType->night_per_km_rate = $request->input('night_per_km_rate');
            $vehicleType->day_per_minute_rate = $request->input('day_per_minute_rate');
            $vehicleType->night_per_minute_rate = $request->input('night_per_minute_rate');
            $vehicleType->is_active = $request->input('is_active', true);

            if ($request->hasFile('icon_url')) {
                if ($vehicleType->icon_url) {
                    $oldPath = 'public' . $vehicleType->icon_url;
                    if (Storage::exists($oldPath)) {
                        Storage::delete($oldPath);
                    }
                }
                $path = $request->file('icon_url')->store('vehicle-icons', 'public');
                $vehicleType->icon_url = '/storage/' . $path;
            }

            $vehicleType->save();

            $new = $vehicleType->toArray();
            AudiLogsService::storeLog('update', 'vehicleType', $vehicleType->id, $old, $new);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle type updated successfully!',
                'data' => $vehicleType,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating vehicle type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $vehicleType = VehicleType::findOrFail($id);
            $old = $vehicleType->toArray();
            $vehicleType->delete();

            AudiLogsService::storeLog('delete', 'vehicleType', $vehicleType->id, $old, null);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle type deleted successfully!',
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting vehicle type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getCommissionPercentage()
    {
        try {
            $commission = VehicleType::value('commission_percentage') ?? 10.00;
            return response()->json([
                'success' => true,
                'commission_percentage' => $commission,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching commission percentage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch commission percentage.',
            ], 500);
        }
    }

    public function updateCommissionPercentage(Request $request)
    {
        try {
            $request->validate([
                'commission_percentage' => 'required|numeric|min:0|max:100',
            ]);

            $oldCommission = VehicleType::value('commission_percentage') ?? 10.00;

            VehicleType::query()->update([
                'commission_percentage' => $request->input('commission_percentage')
            ]);

            AudiLogsService::storeLog(
                'update',
                'vehicleTypeCommission',
                null,
                ['commission_percentage' => $oldCommission],
                ['commission_percentage' => $request->input('commission_percentage')]
            );

            return response()->json([
                'success' => true,
                'message' => 'Commission percentage updated successfully for all vehicle types!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating commission percentage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}