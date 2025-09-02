<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Address;
use App\Models\City;
use App\Models\District;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AudiLogsService;

class AddressController extends Controller
{
    public function getUserAddresses(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $query = $user->addresses()->with(['city', 'district'])->select('addresses.*');

        return DataTables::of($query)
            ->addColumn('actions', function ($address) {
                return '
                 <button class="btn btn-sm btn-danger delete-item" data-id="' . $address->id . '" data-url="' . route('admin.profile.addresses.destroy', $address->id) . '"
                        data-table="#addresses-table">
                            <i class="fas fa-trash"></i> Delete
                    </button>';
            })
            ->addColumn('city_name', function ($address) {
                return $address->city ? $address->city->name_en : 'N/A';
            })
            ->addColumn('district_name', function ($address) {
                return $address->district ? $address->district->name_en : 'N/A';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $userCountryId = $user->city->country->id;
        $cities = City::where('country_id', $userCountryId)->get();

        return response()->json([
            'cities' => $cities
        ]);
    }

    public function store(Request $request, $userId)
    {
        try {
            // Validate the incoming data
            $validated = $request->validate([
                'street' => 'required|string|max:255',
                'city_id' => 'required|exists:cities,id',
                'district_id' => 'required|exists:districts,id',
                'type' => 'required|string|max:50',
            ]);

            $address = new Address($validated);
            $address->user_id = $userId; // Associate the address with the user
            $address->save();

            $new = $address->toArray();
            AudiLogsService::storeLog('create', 'profile->address', $address->id, null, $new);
            return response()->json(['success' => true, 'message' => 'Address created successfully']);
        } catch (\Exception $e) {
            // Catch any exception and log the error
            Log::error("Error creating address: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred. Please try again later.'], 500);
        }
    }


    public function getDistricts(Request $request, $cityId)
    {
        // Fetch the districts for the selected city
        $districts = District::where('city_id', $cityId)->get();

        return response()->json([
            'districts' => $districts
        ]);
    }

    // Delete the address
    public function destroy($id)
    {
        $address = Address::findOrFail($id);

        $old = $address->toArray();
        $address->delete();

        AudiLogsService::storeLog('create', 'locations->address', $address->id, $old, null);
        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully!',
            'id' => $id,
        ]);
    }
}
