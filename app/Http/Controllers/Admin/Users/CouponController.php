<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Services\AudiLogsService;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'min_trip_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'required|boolean'
        ]);

        $coupon = Coupon::create($request->all());
        $new = $coupon->toArray();;
        AudiLogsService::storeLog('create', 'coupon', $coupon->id, null, $new);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'data' => $coupon, 'message' => 'Coupon created successfully.']);
        }
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }


    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'min_trip_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'required|boolean'
        ]);

        $old = $coupon->toArray();;
        $coupon->update($request->all());
        $new = $coupon->toArray();;

        AudiLogsService::storeLog('create', 'coupon', $coupon->id, $old, $new);


        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'data' => $coupon, 'message' => 'Coupon updated successfully.']);
        }
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Request $request, Coupon $coupon)
    {
        $coupon->delete();
        $old = $coupon->toArray();;
        $coupon->update($request->all());
        AudiLogsService::storeLog('create', 'coupon', $coupon->id, $old, null);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Coupon deleted successfully.']);
        }
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }

    public function edit(Coupon $coupon)
    {
        return response()->json($coupon);
    }

    public function getCouponsData(Request $request)
    {
        $coupons = Coupon::withCount('users')
            ->select('id', 'code', 'type', 'value', 'max_uses', 'max_uses_per_user', 'min_trip_amount', 'starts_at', 'expires_at', 'is_active', 'created_at');

        return DataTables::of($coupons)
            ->addColumn('used_by', function ($row) {
                return $row->users()->sum('uses');
            })
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-sm btn-warning edit-coupon" data-id="' . $row->id . '">Edit</button>
                        <form action="' . route('admin.coupons.destroy', $row->id) . '" method="POST" style="display:inline-block;">'
                        . csrf_field() . method_field('DELETE') .
                        '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button></form>';
            })
            ->editColumn('type', function ($row) {
                return $row->type === 'fixed' ? 'Fixed Amount' : 'Percent';
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            })
            ->editColumn('starts_at', function ($row) {
                return $row->starts_at ? date('Y-m-d H:i', strtotime($row->starts_at)) : '-';
            })
            ->editColumn('expires_at', function ($row) {
                return $row->expires_at ? date('Y-m-d H:i', strtotime($row->expires_at)) : '-';
            })
            ->rawColumns(['action', 'is_active'])
            ->make(true);
    }

}
