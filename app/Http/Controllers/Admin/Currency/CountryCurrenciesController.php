<?php

namespace App\Http\Controllers\Admin\Currency;

use Illuminate\Http\Request;
use App\Models\CountryCurrency;
use App\Models\Country;
use App\Models\Currency;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class CountryCurrenciesController extends Controller
{
    public function index()
    {
        return view('admin.countryCurrencies.index');
    }

    public function getData(Request $request)
    {
        $query = CountryCurrency::with(['country', 'currency'])
            ->leftJoin('countries', 'countrycurrencies.country_id', '=', 'countries.id')
            ->leftJoin('currencies', 'countrycurrencies.currency_id', '=', 'currencies.id')
            ->select('countrycurrencies.*');

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('countries.name_ar', 'like', "%{$search}%")
                    ->orWhere('countries.name_en', 'like', "%{$search}%")
                    ->orWhere('currencies.name', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addColumn('country_ar', function ($row) {
                return $row->country ? $row->country->name_ar : '';
            })
            ->addColumn('country_en', function ($row) {
                return $row->country ? $row->country->name_en : '';
            })
            ->addColumn('currency', function ($row) {
                return $row->currency ? $row->currency->name : '';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-danger delete-item" data-id="' . $row->id . '" data-url="' . route('admin.countrycurrencies.destroy', $row->id) . '"
                            data-table="#country-currencies-table">
                                <i class="fas fa-trash"></i> Delete
                        </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'currency_ids' => 'required|array|min:1',
            'currency_ids.*' => 'exists:currencies,id',
        ]);
        $country_id = $request->input('country_id');
        $currency_ids = $request->input('currency_ids');
        $existingCurrencyIds = CountryCurrency::where('country_id', $country_id)
            ->pluck('currency_id')
            ->toArray();
        $countryCurrencies = collect();
        foreach ($currency_ids as $currency_id) {
            if (!in_array($currency_id, $existingCurrencyIds)) {
                $newCurrency = CountryCurrency::create([
                    'country_id' => $country_id,
                    'currency_id' => $currency_id,
                ]);
                $countryCurrencies->push($newCurrency);
            }
        }
        if ($countryCurrencies->isNotEmpty()) {
            \App\Services\AudiLogsService::storeLog('create', 'countrycurrencies', null, null, $countryCurrencies->toArray());
            return response()->json([
                'success' => true,
                'message' => 'New currencies have been successfully added!',
                'data' => $countryCurrencies,
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'No new currencies to add. All selected currencies already exist for this country.',
                'data' => [],
            ]);
        }
    }
    public function getFormOptions()
    {
        $countries_en = Country::select('id', 'name_en')->get();
        $countries_ar = Country::select('id', 'name_en')->get();
        $currencies = Currency::select('id', 'name')->get();
        return response()->json([
            'countries_en' => $countries_en,
            'countries_ar' => $countries_ar,
            'currencies' => $currencies,
        ]);
    }
    public function getCountryCurrencies($country_id)
    {
        $currency_ids = CountryCurrency::where('country_id', $country_id)
            ->pluck('currency_id');
        return response()->json(['currency_ids' => $currency_ids]);
    }

    public function destroy($id)
    {
        $row = CountryCurrency::find($id);
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Link not found'], 404);
        }
        $old = $row->toArray();
        $row->delete();
        \App\Services\AudiLogsService::storeLog('delete', 'countrycurrencies', $id, $old, null);
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!',
            'id' => $id,
        ]);
    }

}
