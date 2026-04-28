<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        return view('audit-logs.index', [
            'logs' => AuditLog::query()
                ->with('user')
                ->latest('created_at')
                ->paginate(20),
        ]);
    }
}
