<?php

namespace App\Http\Controllers\Admin\Locations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Services\AudiLogsService;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class CountriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.locations.countries.index');
    }

    public function getCountriesData(Request $request)
    {
        $countries = Country::select('id', 'name_en', 'name_ar', 'code', 'created_at')->get();

        return DataTables::of($countries)
         ->addColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-info edit-item" data-id="' . $row->id . '"
                                data-url="' . route('admin.countries.update', $row->id) . '" data-table="#countries-table">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-item" data-id="' . $row->id . '" data-url="' . route('admin.countries.destroy', $row->id) . '"
                            data-table="#countries-table">
                                <i class="fas fa-trash"></i> Delete
                        </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getAllCountries()
    {
        // Fetch all countries
        $countries = Country::select('id', 'name_en')->get();

        // Return the countries in JSON format
        return response()->json([
            'success' => true,
            'data' => $countries,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.locations.countries.create'); // Show the form to create a new country
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
                'code' => 'required|string|size:3|unique:countries,code',
            ]);

            // Create the new country
            $country = new Country();
            $country->name_en = $request->input('name_en');
            $country->name_ar = $request->input('name_ar');
            $country->code = $request->input('code');

            $country->save();
            $new = $country->toArray();
            AudiLogsService::storeLog('create', 'locations->country', $country->id, null, $new);

            return response()->json([
                'success' => true,
                'message' => 'Country created successfully!',
                'data' => $country,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating country: ' . $e->getMessage());

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
        $country = Country::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $country,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $country = Country::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $country,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching country data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch country data.',
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
                'code' => 'required|string|size:3|unique:countries,code,' . $id,
            ]);

            // Find the country by ID
            $country = Country::findOrFail($id);
            $old = $country->toArray();

            $country->name_en = $request->input('name_en');
            $country->name_ar = $request->input('name_ar');
            $country->code = $request->input('code');

            $country->save();
            $new = $country->toArray();
            AudiLogsService::storeLog('update', 'locations->country', $country->id, $old, $new);
            return response()->json([
                'success' => true,
                'message' => 'Country updated successfully!',
                'data' => $country,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating country: ' . $e->getMessage());

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
            $country = Country::findOrFail($id);
            $country->delete();
            $old = $country->toArray();
            AudiLogsService::storeLog('delete', 'locations->country', $country->id, $old, null);

            return response()->json([
                'success' => true,
                'message' => 'Country deleted successfully!',
                'id' => $id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting country: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
