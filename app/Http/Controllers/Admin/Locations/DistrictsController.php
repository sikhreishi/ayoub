<?php

namespace App\Http\Controllers\Admin\Locations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\District;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Services\AudiLogsService;

class DistrictsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.locations.districts.index'); // View for listing districts
    }

    /**
     * Get districts data for DataTable.
     */
    public function getDistrictsData(Request $request)
    {
        // Fetch districts with their associated city name
        $districts = District::with('city') // Eager load city relation
            ->select('id', 'name_en', 'name_ar', 'city_id', 'lat', 'lng', 'created_at')
            ->get();

        return DataTables::of($districts)
            ->addColumn('city_name', function ($row) {
                return $row->city ? $row->city->name_en : 'N/A'; // Access city name via the relation
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-info edit-item" data-id="' . $row->id . '"
                                data-url="' . route('admin.districts.update', $row->id) . '" data-table="#districts-table">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-item" data-id="' . $row->id . '" data-url="' . route('admin.districts.destroy', $row->id) . '"
                            data-table="#districts-table">
                                <i class="fas fa-trash"></i> Delete
                        </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function getAll(Request $request)
    {
        // Get all districts
        $districts = District::all();

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.locations.districts.create'); // Show the form to create a new district
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'name_en' => 'required|string|max:100',
                'name_ar' => 'nullable|string|max:100',
                'city_id' => 'required|exists:cities,id', // Ensure the city exists
                'lat' => 'nullable|numeric', // Latitude validation
                'lng' => 'nullable|numeric', // Longitude validation
            ]);

            // Create the new district
            $district = new District();

            $district->name_en = $request->input('name_en');
            $district->name_ar = $request->input('name_ar');
            $district->city_id = $request->input('city_id');
            $district->lat = $request->input('lat');
            $district->lng = $request->input('lng');
            $district->save();
            $new = $district->toArray();
            AudiLogsService::storeLog('create', 'locations->district', $district->id, null, $new);
            return response()->json([
                'success' => true,
                'message' => 'District created successfully!',
                'data' => $district,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating district: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Create failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $district = District::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $district,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $district = District::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $district,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching district data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch district data.',
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
                'name_en' => 'required|string|max:100',
                'name_ar' => 'nullable|string|max:100',
                'city_id' => 'required|exists:cities,id', // Ensure the city exists
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
            ]);

            // Find the district by ID
            $district = District::findOrFail($id);
            $old = $district->toArray();

            $district->name_en = $request->input('name_en');
            $district->name_ar = $request->input('name_ar');
            $district->city_id = $request->input('city_id');
            $district->lat = $request->input('lat');
            $district->lng = $request->input('lng');

            $district->save();
            $new = $district->toArray();
            AudiLogsService::storeLog('update', 'locations->district', $district->id, $old, $new);
            return response()->json([
                'success' => true,
                'message' => 'District updated successfully!',
                'data' => $district,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating district: ' . $e->getMessage());

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
            $district = District::findOrFail($id);
            $old = $district->toArray();
            $district->delete();

            AudiLogsService::storeLog('delete', 'locations->district', $district->id, $old, null);
            return response()->json([
                'success' => true,
                'message' => 'District deleted successfully!',
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting district: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
