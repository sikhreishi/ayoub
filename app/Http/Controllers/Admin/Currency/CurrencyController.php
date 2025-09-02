<?php

namespace App\Http\Controllers\Admin\Currency;

use Illuminate\Http\Request;
use App\Models\Currency;
use Yajra\DataTables\DataTables;
use App\Services\AudiLogsService;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    public function index()
    {

        return view('admin.currency.index');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:currencies,code',
        ]);

        $currency = Currency::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
        ]);
        // Audit log
        AudiLogsService::storeLog('create', 'currencies', $currency->id, null, $currency->toArray());
        return response()->json([
            'success' => true,
            'message' => 'country Currency created successfully!',
            'data' => $currency,
        ]);
        // return response()->json(['success' => true, 'currency'=>$currency]);
    }
    public function getData()
    {
        $currencies = Currency::all();
        return DataTables::of($currencies)
            ->addColumn('name', function ($currency) {
                return $currency->name ? $currency->name : null;
            })
            ->addColumn('code', function ($currency) {
                return $currency->code ? $currency->code : null;
            })
            ->addColumn('action', function ($currency) {
                return '<button class="btn btn-sm btn-danger delete-item" data-id="' . $currency->id . '">Delete</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        $old = $currency->toArray();
        $currency->delete();
        // Audit log
        AudiLogsService::storeLog('delete', 'currencies', $id, $old, null);
        return response()->json(['success' => true]);
    }
}
