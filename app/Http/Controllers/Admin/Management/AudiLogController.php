<?php


namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AudiLog;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class AudiLogController extends Controller
{
    // عرض جميع السجلات
    public function index()
    {
        $logs = AudiLog::with('admin')->orderBy('created_at', 'desc')->paginate(30);
        return view('admin.audi_logs.index', compact('logs'));
    }



    // DataTable AJAX provider
    public function dataTableAjax()
    {
        $logs = AudiLog::with('admin')->orderBy('created_at', 'desc');
        return DataTables::of($logs)
            ->addColumn('admin', function ($log) {
                return $log->admin?->name ?? '-';
            })
            ->addColumn('old_values', function ($log) {
                $old = is_array($log->old_values) ? $log->old_values : (array) json_decode($log->old_values, true);
                $new = is_array($log->new_values) ? $log->new_values : (array) json_decode($log->new_values, true);
                $exclude = ['created_at', 'updated_at'];
                $diff = [];
                foreach ($old as $k => $v) {
                    if (in_array($k, $exclude))
                        continue;
                    if (!array_key_exists($k, $new) || $new[$k] != $v) {
                        $diff[$k] = $v;
                    }
                }
                if (empty($diff))
                    return '<span class="text-muted">No change</span>';
                $html = '<div style="background:#f8f9fa;border:1px solid #e3e3e3;padding:8px 12px;border-radius:6px;font-family:monospace;font-size:13px;max-width:350px;overflow-x:auto;">';
                foreach ($diff as $k => $v) {
                    $html .= '<div><span style="color:#0d6efd;font-weight:bold;">' . e($k) . '</span>: <span style="color:#dc3545;">' . e(is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v) . '</span></div>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('new_values', function ($log) {
                $old = is_array($log->old_values) ? $log->old_values : (array) json_decode($log->old_values, true);
                $new = is_array($log->new_values) ? $log->new_values : (array) json_decode($log->new_values, true);
                $exclude = ['created_at', 'updated_at'];
                $diff = [];
                foreach ($new as $k => $v) {
                    if (in_array($k, $exclude))
                        continue;
                    if (!array_key_exists($k, $old) || $old[$k] != $v) {
                        $diff[$k] = $v;
                    }
                }
                if (empty($diff))
                    return '<span class="text-muted">No change</span>';
                $html = '<div style="background:#eaf7ea;border:1px solid #b6e2b6;padding:8px 12px;border-radius:6px;font-family:monospace;font-size:13px;max-width:350px;overflow-x:auto;">';
                foreach ($diff as $k => $v) {
                    $html .= '<div><span style="color:#198754;font-weight:bold;">' . e($k) . '</span>: <span style="color:#222;">' . e(is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v) . '</span></div>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('user_agent', function ($log) {
                $ua = $log->user_agent;
                // Simple browser/OS extraction (not perfect, but better UX)
                $browser = $os = '';
                if (strpos($ua, 'Windows') !== false)
                    $os = 'Windows';
                elseif (strpos($ua, 'Macintosh') !== false)
                    $os = 'Mac';
                elseif (strpos($ua, 'Linux') !== false)
                    $os = 'Linux';
                elseif (strpos($ua, 'Android') !== false)
                    $os = 'Android';
                elseif (strpos($ua, 'iPhone') !== false)
                    $os = 'iPhone';
                elseif (strpos($ua, 'iPad') !== false)
                    $os = 'iPad';
                else
                    $os = 'Other';

                if (preg_match('/(Chrome|Firefox|Safari|Edge|Opera|MSIE|Trident)/i', $ua, $matches)) {
                    $browser = $matches[1];
                    if ($browser == 'Trident' || $browser == 'MSIE')
                        $browser = 'IE';
                } else {
                    $browser = 'Other';
                }
                $short = $browser . ' / ' . $os;
                return '<span class="badge bg-info text-dark" title="' . e($ua) . '">' . e($short) . '</span>';
            })
            ->addColumn('created_at', function ($log) {
                $carbon = Carbon::parse($log->created_at);
                $human = $carbon->diffForHumans();
                $full = $carbon->format('Y-m-d H:i:s');
                return '<span title="' . e($full) . '">' . e($human) . '</span>';
            })
            ->rawColumns(['old_values', 'new_values', 'created_at', 'user_agent'])
            ->make(true);
    }
}
