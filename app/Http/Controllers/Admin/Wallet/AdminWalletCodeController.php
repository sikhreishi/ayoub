<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WalletCode;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Services\AudiLogsService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class AdminWalletCodeController extends Controller
{
    public function index()
    {
        return view('admin.wallet-codes.index');
    }

    public function getCodesData(Request $request)
    {
        $query = WalletCode::with(['generatedBy', 'usedBy'])
            ->select('id', 'code', 'balance', 'status', 'generated_by', 'used_by', 'created_at', 'used_at')
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $codes = $query->get();

        return DataTables::of($codes)
            ->addColumn('generated_by_name', function ($row) {
                return $row->generatedBy ? $row->generatedBy->name : 'N/A';
            })
            ->addColumn('used_by_name', function ($row) {
                return $row->usedBy ? $row->usedBy->name : 'N/A';
            })
            ->addColumn('status_badge', function ($row) {
                $badgeClass = $row->status === 'used' ? 'bg-success' : 'bg-warning';
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('formatted_balance', function ($row) {
                return '$' . number_format($row->balance, 2);
            })
            ->addColumn('formatted_created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i');
            })
            ->addColumn('formatted_used_at', function ($row) {
                return $row->used_at ? $row->used_at->format('Y-m-d H:i') : 'N/A';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-danger delete-item" data-id="' . $row->id . '" data-url="' . route('admin.wallet-codes.destroy', $row->id) . '" data-table="#wallet-codes-table">
                    <i class="material-icons-outlined">delete</i>
                    Delete
                </button>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'balance' => 'required|numeric|min:0.01|max:9999999.99',
                'quantity' => 'required|integer|min:1|max:100'
            ]);

            $generatedCodes = [];

            for ($i = 0; $i < $request->quantity; $i++) {
                $code = WalletCode::create([
                    'code' => WalletCode::generateUniqueCode(),
                    'balance' => $request->balance,
                    'generated_by' => auth()->id()
                ]);

                $generatedCodes[] = $code;
            }

            AudiLogsService::storeLog('create', 'wallet_codes', null, null, [
                'quantity' => $request->quantity,
                'balance' => $request->balance,
                'generated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully generated {$request->quantity} wallet codes!",
                'codes' => $generatedCodes
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating wallet codes: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate codes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $code = WalletCode::findOrFail($id);

            if ($code->status === 'used') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete used wallet codes'
                ], 400);
            }

            $old = $code->toArray();
            $code->delete();

            AudiLogsService::storeLog('delete', 'wallet_codes', $id, $old, null);

            return response()->json([
                'success' => true,
                'message' => 'Wallet code deleted successfully!',
                'id' => $id
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting wallet code: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $codes = WalletCode::with(['generatedBy', 'usedBy'])
                ->orderBy('created_at', 'desc')
                ->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Code');
            $sheet->setCellValue('B1', 'Balance');
            $sheet->setCellValue('C1', 'Status');
            $sheet->setCellValue('D1', 'Generated By');
            $sheet->setCellValue('E1', 'Used By');
            $sheet->setCellValue('F1', 'Created At');
            $sheet->setCellValue('G1', 'Used At');

            $row = 2;
            foreach ($codes as $code) {
                $sheet->setCellValue('A' . $row, $code->code);
                $sheet->setCellValue('B' . $row, '$' . number_format($code->balance, 2));
                $sheet->setCellValue('C' . $row, ucfirst($code->status));
                $sheet->setCellValue('D' . $row, $code->generatedBy ? $code->generatedBy->name : 'N/A');
                $sheet->setCellValue('E' . $row, $code->usedBy ? $code->usedBy->name : 'N/A');
                $sheet->setCellValue('F' . $row, $code->created_at->format('Y-m-d H:i:s'));
                $sheet->setCellValue('G' . $row, $code->used_at ? $code->used_at->format('Y-m-d H:i:s') : 'N/A');
                $row++;
            }

            foreach (range('A', 'G') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'wallet_codes_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filePath = storage_path('app/temp/' . $fileName);

            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $writer->save($filePath);

            return Response::download($filePath, $fileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Error exporting wallet codes: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }
}