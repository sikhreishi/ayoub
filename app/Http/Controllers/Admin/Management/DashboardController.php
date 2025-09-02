<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Trip;


class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalUsers = User::count();
        $totalDrivers = User::role('driver')->count();
        $totalTrips = Trip::count();
        $completedTrips = Trip::where('status', 'completed')->count();
        $cancelledTrips = Trip::where('status', 'cancelled')->count();
        $ongoingTrips = Trip::whereNotIn('status', ['completed', 'cancelled'])->count();
        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalDrivers' => $totalDrivers,
            'totalTrips' => $totalTrips,
            'completedTrips' => $completedTrips,
            'cancelledTrips' => $cancelledTrips,
            'ongoingTrips' => $ongoingTrips,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
