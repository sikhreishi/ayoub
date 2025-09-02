<?php

namespace App\Http\Controllers\Admin\Drivers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use App\Models\DriverAvailability;

class DriversAvailabilityController extends Controller
{

    public function index(Request $request)
    {
        return view('admin.drivers.availability');
    }

    public function UnavailableIndex(Request $request)
    {
        return view('admin.drivers.unAvailability');
    }

    public function getDriversAvailabilityData(Request $request)
    {
        // Fetch drivers who are marked as available but have a pending trip
        $drivers = DriverAvailability::with('driver') // Eager load the user (driver) data
            ->select('driver_availability.driver_id', 'driver_availability.last_ping', 'driver_availability.is_available')
            ->whereHas('driver.roles', function ($query) {
                $query->where('name', 'driver'); // Ensure the driver role is present
            })
            ->whereHas('driver.driverProfile', function ($query) {
                $query->where('is_driver_verified', 1); // Ensure the driver is verified
            })
            ->where('is_available', true) // Only consider drivers who are available
            ->whereDoesntHave('driver.driverTrips', function ($query) {
                $query->where('status', 'in_progress'); // Only include drivers who have a pending trip
            })
            ->get();

        return DataTables::of($drivers)
            ->addColumn('name', function ($row) {
                return $row->driver ? $row->driver->name : 'N/A'; // Access driver name
            })
            ->addColumn('phone', function ($row) {
                return $row->driver ? $row->driver->phone : 'N/A'; // Access driver phone
            })
            ->addColumn('avatar', function ($row) {
                return $row->driver && $row->driver->avatar
                    ? '<img src="' . asset('storage/' . $row->driver->avatar) . '" alt="Avatar" width="50" height="50">'
                    : 'No Avatar'; // Handle Avatar
            })
            ->addColumn('last_ping', function ($row) {
                return $row->last_ping ? $row->last_ping->format('Y-m-d H:i') : 'N/A'; // Format last_ping datetime
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.drivers.show', $row->driver_id) . '" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Profile
                    </a>';
            })
            ->rawColumns(['avatar', 'action']) // Allow raw HTML for avatar and actions
            ->make(true);
    }


    public function getUnavailableDriversData(Request $request)
    {
        $drivers = DriverAvailability::with('driver') 
            ->select('driver_availability.driver_id', 'driver_availability.last_ping', 'driver_availability.is_available')
            ->whereHas('driver.roles', function ($query) {
                $query->where('name', 'driver'); 
            })
            ->whereHas('driver.driverProfile', function ($query) {
                $query->where('is_driver_verified', 1); 
            })
            ->where('is_available', true) 
            ->whereHas('driver.driverTrips', function ($query) {
                $query->where('status', 'in_progress'); 
            })
            ->get();

        return DataTables::of($drivers)
            ->addColumn('name', function ($row) {
                return $row->driver ? $row->driver->name : 'N/A'; 
            })
            ->addColumn('phone', function ($row) {
                return $row->driver ? $row->driver->phone : 'N/A'; 
            })
            ->addColumn('avatar', function ($row) {
                return $row->driver && $row->driver->avatar
                    ? '<img src="' . asset('storage/' . $row->driver->avatar) . '" alt="Avatar" width="50" height="50">'
                    : 'No Avatar'; 
            })
            ->addColumn('last_ping', function ($row) {
                return $row->last_ping ? $row->last_ping->format('Y-m-d H:i') : 'N/A'; 
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.drivers.show', $row->driver_id) . '" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Profile
                    </a>';
            })
            ->rawColumns(['avatar', 'action']) 
            ->make(true);
    }
}
