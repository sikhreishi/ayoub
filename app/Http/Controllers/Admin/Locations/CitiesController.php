<?php

namespace App\Http\Controllers\Admin\Locations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Services\AudiLogsService;

class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.locations.cities.index'); // View for listing cities
    }

    /**
     * Get cities data for DataTable.
     */
    public function getCitiesData(Request $request)
    {
        // Eager load the country relationship to access country name
        $cities = City::with('country') // Make sure you load the 'country' relationship
            ->select('id', 'name_en', 'name_ar', 'country_id', 'lat', 'lng', 'created_at')
            ->get();

        return DataTables::of($cities)
            ->addColumn('country_name', function ($row) {
                // Return the country name using the loaded relationship
                return $row->country ? $row->country->name_en : 'N/A'; // If country exists, show its name
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-info edit-item" data-id="' . $row->id . '"
                            data-url="' . route('admin.cities.update', $row->id) . '" data-table="#cities-table">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger delete-item" data-id="' . $row->id . '" data-url="' . route('admin.cities.destroy', $row->id) . '"
                        data-table="#cities-table">
                            <i class="fas fa-trash"></i> Delete
                    </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getAllCities()
    {
        // Fetch all cities
        $cities = City::select('id', 'name_en')->get();

        // Return the cities in JSON format
        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    public function getCities($countryId)
    {
        $cities = City::where('country_id', $countryId)->get(['id', 'name_en']);

        return response()->json([
            'cities' => $cities
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.locations.cities.create'); // Show the form to create a new city
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'name_en' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'country_id' => 'required|exists:countries,id', // Ensure the country exists
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
            ]);

            // Create the new city
            $city = new City();
            $city->name_en = $request->input('name_en');
            $city->name_ar = $request->input('name_ar');
            $city->country_id = $request->input('country_id');
            $city->lat = $request->input('lat');
            $city->lng = $request->input('lng');

            $city->save();
            AudiLogsService::storeLog('create', 'locations->cities', $city->id, null, $city->toArray());

            return response()->json([
                'success' => true,
                'message' => 'City created successfully!',
                'data' => $city,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating city: ' . $e->getMessage());

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
        $city = City::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $city,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $city = City::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $city,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching city data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch city data.',
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
                'name_en' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'country_id' => 'required|exists:countries,id', // Ensure the country exists
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
            ]);

            // Find the city by ID
            $city = City::findOrFail($id);
            $old = $city->toArray();
            $city->name_en = $request->input('name_en');
            $city->name_ar = $request->input('name_ar');
            $city->country_id = $request->input('country_id');
            $city->lat = $request->input('lat');
            $city->lng = $request->input('lng');

            $city->save();
            $new = $city->toArray();
            AudiLogsService::storeLog('update', 'locations->cities', $city->id, $old, $new);

            return response()->json([
                'success' => true,
                'message' => 'City updated successfully!',
                'data' => $city,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating city: ' . $e->getMessage());

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
            $city = City::findOrFail($id);
            $city->delete();
            $old = $city->toArray();
            AudiLogsService::storeLog('delete', 'locations->cities', $city->id, $old, null);

            return response()->json([
                'success' => true,
                'message' => 'City deleted successfully!',
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting city: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
