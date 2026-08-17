<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use App\Services\BulkSmsNigeriaService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * How many days a check-in should keep a member showing as "Present"
     * on the dashboard before they roll over into "Absent".
     */
    protected const PRESENT_WINDOW_DAYS = 6;

    public function dashboard(Request $request)
    {
        // Dynamically captures whatever date is input (even into June/July)
        $date = $request->get('date', Carbon::today()->toDateString());

        $todayAttendance = Attendance::with('member')
            ->whereDate('attendance_date', $date)
            ->latest('submitted_at')
            ->get();

        $totalMembers = Member::where('is_active', true)->count();
        $rate = $totalMembers > 0 ? round(($todayAttendance->count() / $totalMembers) * 100) : 0;

        // ── Rolling present/absent window ──────────────────────────────
        // A member who checked in any time in the last N days (inclusive of
        // the selected date) stays "Present". Everyone else is "Absent".
        $windowDays  = self::PRESENT_WINDOW_DAYS;
        $windowStart = Carbon::parse($date)->subDays($windowDays - 1)->toDateString();

        [$rollingAttendance, $absentMembers] = $this->presentAndAbsent($windowStart, $date);

        return view('admin.dashboard', compact(
            'todayAttendance',
            'date',
            'totalMembers',
            'rate',
            'rollingAttendance',
            'absentMembers',
            'windowStart',
            'windowDays'
        ));
    }

    /**
     * Manually trigger an SMS to every active member who has not checked in
     * within the rolling present window ending on the given date.
     */
    public function sendAbsentSms(Request $request, BulkSmsNigeriaService $sms)
    {
        $request->validate([
            'date'    => 'nullable|date',
            'message' => 'required|string|max:459',
        ]);

        $date        = $request->get('date', Carbon::today()->toDateString());
        $windowDays  = self::PRESENT_WINDOW_DAYS;
        $windowStart = Carbon::parse($date)->subDays($windowDays - 1)->toDateString();

        [, $absentMembers] = $this->presentAndAbsent($windowStart, $date);

        $recipients = $absentMembers
            ->filter(fn ($m) => !empty($m->phone))
            ->pluck('phone')
            ->all();

        if (empty($recipients)) {
            return redirect()
                ->route('admin.dashboard', ['date' => $date])
                ->with('sms_status', 'none')
                ->with('sms_audience', 'absent');
        }

        $result = $sms->sendToMany($recipients, $request->input('message'));

        return redirect()
            ->route('admin.dashboard', ['date' => $date])
            ->with('sms_status', $result['success'] ? 'sent' : 'error')
            ->with('sms_sent_count', count($result['sent_to']))
            ->with('sms_skipped_count', count($result['skipped']))
            ->with('sms_error', $result['error'])
            ->with('sms_audience', 'absent');
    }

    /**
     * Manually trigger an SMS to every member who HAS checked in within the
     * rolling present window ending on the given date.
     */
    public function sendPresentSms(Request $request, BulkSmsNigeriaService $sms)
    {
        $request->validate([
            'date'    => 'nullable|date',
            'message' => 'required|string|max:459',
        ]);

        $date        = $request->get('date', Carbon::today()->toDateString());
        $windowDays  = self::PRESENT_WINDOW_DAYS;
        $windowStart = Carbon::parse($date)->subDays($windowDays - 1)->toDateString();

        [$rollingAttendance, ] = $this->presentAndAbsent($windowStart, $date);

        $recipients = $rollingAttendance
            ->filter(fn ($a) => !empty($a->member?->phone))
            ->pluck('member.phone')
            ->unique()
            ->values()
            ->all();

        if (empty($recipients)) {
            return redirect()
                ->route('admin.dashboard', ['date' => $date])
                ->with('sms_status', 'none')
                ->with('sms_audience', 'present');
        }

        $result = $sms->sendToMany($recipients, $request->input('message'));

        return redirect()
            ->route('admin.dashboard', ['date' => $date])
            ->with('sms_status', $result['success'] ? 'sent' : 'error')
            ->with('sms_sent_count', count($result['sent_to']))
            ->with('sms_skipped_count', count($result['skipped']))
            ->with('sms_error', $result['error'])
            ->with('sms_audience', 'present');
    }

    /**
     * Stream a CSV of members currently "Present" within the rolling window
     * ending on the given date (defaults to today).
     */
    public function exportPresent(Request $request)
    {
        $request->validate(['date' => 'nullable|date']);

        $date        = $request->get('date', Carbon::today()->toDateString());
        $windowDays  = self::PRESENT_WINDOW_DAYS;
        $windowStart = Carbon::parse($date)->subDays($windowDays - 1)->toDateString();

        [$rollingAttendance, ] = $this->presentAndAbsent($windowStart, $date);

        $filename = "present_{$windowStart}_to_{$date}.csv";

        return response()->streamDownload(function () use ($rollingAttendance) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Phone', 'Church', 'Check-in Date', 'Children']);

            foreach ($rollingAttendance as $a) {
                $m = $a->member;

                $dateString = ($a->attendance_date instanceof Carbon)
                    ? $a->attendance_date->format('Y-m-d')
                    : Carbon::parse($a->attendance_date)->format('Y-m-d');

                fputcsv($handle, [
                    $m ? "{$m->first_name} {$m->last_name}" : 'Unknown',
                    $a->phone ?? ($m->phone ?? 'N/A'),
                    $m->church ?? 'Unknown',
                    $dateString,
                    $a->children_count ?? 0,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Stream a CSV of active members currently "Absent" within the rolling
     * window ending on the given date (defaults to today).
     */
    public function exportAbsent(Request $request)
    {
        $request->validate(['date' => 'nullable|date']);

        $date        = $request->get('date', Carbon::today()->toDateString());
        $windowDays  = self::PRESENT_WINDOW_DAYS;
        $windowStart = Carbon::parse($date)->subDays($windowDays - 1)->toDateString();

        [, $absentMembers] = $this->presentAndAbsent($windowStart, $date);

        $filename = "absent_{$windowStart}_to_{$date}.csv";

        return response()->streamDownload(function () use ($absentMembers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Phone', 'Church']);

            foreach ($absentMembers as $m) {
                fputcsv($handle, [
                    "{$m->first_name} {$m->last_name}",
                    $m->phone ?? 'No phone',
                    $m->church ?? 'Unknown',
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Shared helper: given a date window, return [rollingAttendance, absentMembers].
     *
     * $rollingAttendance = Attendance rows within the window, one per member
     *                      (their most recent check-in), most recent first.
     * $absentMembers     = active Members with no check-in inside the window.
     */
    protected function presentAndAbsent(string $windowStart, string $windowEnd): array
    {
        $rollingAttendance = Attendance::with('member')
            ->whereDate('attendance_date', '>=', $windowStart)
            ->whereDate('attendance_date', '<=', $windowEnd)
            ->whereNotNull('member_id')
            ->latest('submitted_at')
            ->get()
            ->unique('member_id')
            ->values();

        $presentMemberIds = $rollingAttendance->pluck('member_id')->all();

        $absentMembers = Member::where('is_active', true)
            ->whereNotIn('id', $presentMemberIds)
            ->orderBy('first_name')
            ->get();

        return [$rollingAttendance, $absentMembers];
    }

    public function rankings(Request $request)
    {
        $from = $request->get('from', '2024-01-01');
        $to   = $request->get('to', Carbon::today()->toDateString());

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
        $to    = $request->get('to', Carbon::today()->toDateString());
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
            fputcsv($handle, ['First Name', 'Last Name', 'Phone', 'Group', 'Church', 'Cell', 'Birthday', 'Status', 'Service', 'Date']);
            foreach ($records as $a) {
                $m = $a->member;

                // Safe formatting fallback checking if attendance_date is properly cast
                $dateString = ($a->attendance_date instanceof Carbon)
                    ? $a->attendance_date->format('Y-m-d')
                    : Carbon::parse($a->attendance_date)->format('Y-m-d');

                fputcsv($handle, [
                    $m?->first_name ?? '',
                    $m?->last_name  ?? '',
                    $m?->phone      ?? $a->phone ?? '',
                    $m?->group      ?? '',
                    $m?->church     ?? '',
                    $m?->cell       ?? '',
                    $m?->birthday   ?? '',
                    'Present',
                    'Sunday Service',
                    $dateString,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
