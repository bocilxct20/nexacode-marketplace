<?php

namespace App\Http\Controllers;

use App\Models\SecurityLog;
use App\Services\SecurityService;
use Illuminate\Http\Request;

class SecurityDashboardController extends Controller
{
    protected $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Show security dashboard
     */
    public function index()
    {
        $user = auth()->user();

        $recentActivity = SecurityLog::getRecentActivity($user->id, 10);
        $twoFactorEnabled = $user->two_factor_enabled;
        $passwordNeedsChange = $this->securityService->needsPasswordChange($user);

        return view('security.index', compact(
            'recentActivity',
            'twoFactorEnabled',
            'passwordNeedsChange'
        ));
    }

    /**
     * Show security logs
     */
    public function logs(Request $request)
    {
        $user = auth()->user();

        $query = SecurityLog::where('user_id', $user->id);

        // Filter by action
        if ($request->action) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->from && $request->to) {
            $query->dateRange($request->from, $request->to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('security.logs', compact('logs'));
    }

    /**
     * Export security logs
     */
    public function exportLogs(Request $request)
    {
        $user = auth()->user();

        $logs = SecurityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $csv = "Date,Action,IP Address,User Agent,Status\n";

        foreach ($logs as $log) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s\n",
                $log->created_at->format('Y-m-d H:i:s'),
                $log->action,
                $log->ip_address,
                str_replace(',', ';', $log->user_agent ?? ''),
                $log->response_status ?? ''
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="security_logs_' . date('Y-m-d') . '.csv"');
    }
}
