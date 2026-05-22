<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $todayAttendance = Attendance::with('member')
            ->whereDate('attendance_date', $date)
            ->latest('submitted_at')
            ->get();

        $totalMembers = Member::where('is_active', true)->count();
        $rate = $totalMembers > 0 ? round(($todayAttendance->count() / $totalMembers) * 100) : 0;

        return view('admin.dashboard', compact('todayAttendance', 'date', 'totalMembers', 'rate'));
    }

    public function rankings(Request $request)
    {
        $from = $request->get('from', '2024-01-01');
        $to   = $request->get('to', now()->toDateString());

        $attendances = Attendance::with('member')
            ->whereDate('attendance_date', '>=', $from)
            ->whereDate('attendance_date', '<=', $to)
            ->whereNotNull('member_id')
            ->get();

        // Top Cells
        $cells = $attendances->groupBy(fn($a) => $a->member->cell ?? 'Unknown')
            ->map(fn($g) => ['name' => $g->first()->member->cell ?? 'Unknown', 'count' => $g->count()])
            ->sortByDesc('count')->take(10)->values();

        // Top Groups
        $groups = $attendances->groupBy(fn($a) => $a->member->group ?? 'Unknown')
            ->map(fn($g) => ['name' => $g->first()->member->group ?? 'Unknown', 'count' => $g->count()])
            ->sortByDesc('count')->take(10)->values();

        // Top Churches
        $churches = $attendances->groupBy(fn($a) => $a->member->church ?? 'Unknown')
            ->map(fn($g) => ['name' => $g->first()->member->church ?? 'Unknown', 'count' => $g->count()])
            ->sortByDesc('count')->take(10)->values();

        return view('admin.rankings', compact('cells', 'groups', 'churches', 'from', 'to'));
    }

    public function exportPage()
    {
        $totalRecords = Attendance::count();
        $totalMembers = Member::where('is_active', true)->count();
        $churches = Member::distinct()->pluck('church')->sort()->values();
        return view('admin.export', compact('totalRecords', 'totalMembers', 'churches'));
    }

    public function exportCsv(Request $request)
    {
        $from  = $request->get('from', '2024-01-01');
        $to    = $request->get('to', now()->toDateString());
        $group = $request->get('group');

        $query = Attendance::with('member')
            ->whereDate('attendance_date', '>=', $from)
            ->whereDate('attendance_date', '<=', $to);

        if ($group) {
            $query->whereHas('member', fn($q) => $q->where('group', $group));
        }

        $records = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_export.csv"',
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['First Name','Last Name','Email','Phone','Group','Church','Cell','Birthday','Status','Service','Date']);
            foreach ($records as $a) {
                $m = $a->member;
                fputcsv($handle, [
                    $m?->first_name ?? '',
                    $m?->last_name  ?? '',
                    $a->email,
                    $m?->phone   ?? '',
                    $m?->group   ?? '',
                    $m?->church  ?? '',
                    $m?->cell    ?? '',
                    $m?->birthday?->format('Y-m-d') ?? '',
                    'Present',
                    'Sunday Service',
                    $a->attendance_date->format('Y-m-d'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
