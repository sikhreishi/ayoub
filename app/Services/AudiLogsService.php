<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\AudiLog;

class AudiLogsService
{
    public static function storeLog($action, $table, $recordId = null, $old = null, $new = null)
    {
        $admin = Auth::user();
        AudiLog::create([
            'admin_id'   => $admin->id,
            'action'     => $action,
            'table_name' => $table,
            'record_id'  => $recordId,
            'old_values' => $old ? json_encode($old) : null,
            'new_values' => $new ? json_encode($new) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }
}
