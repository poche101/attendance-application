<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('public.checkin');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email|max:255|required_without:phone',
            'phone' => 'nullable|string|max:20|required_without:email',
        ]);

        $today = now()->toDateString();
        $member = null;
        $attemptedIdentifier = null;
        $identifierType = null;

        // ── Email path (primary) ──────────────────────────────────────────
        if ($request->filled('email')) {
            $email = strtolower(trim($request->input('email')));
            $attemptedIdentifier = $email;
            $identifierType = 'email';
            $member = Member::where('email', $email)->first();
        }

        // ── Phone path (optional fallback via modal) ──────────────────────
        if (!$member && $request->filled('phone')) {
            $raw    = trim($request->input('phone'));
            $digits = preg_replace('/\D/', '', $raw);
            if (str_starts_with($digits, '234') && strlen($digits) > 10) {
                $digits = substr($digits, 3);
            }
            if (!str_starts_with($digits, '0')) {
                $digits = '0' . $digits;
            }
            $phone = $digits;
            $attemptedIdentifier = $phone;
            $identifierType = 'phone';
            $member = Member::where('phone', $phone)->first();
        }

        // 1. Member not found
        if (!$member) {
            return back()
                ->with('status', 'not_found')
                ->with('attempted_' . $identifierType, $attemptedIdentifier);
        }

        // 2. Pending activation
        if (!$member->is_active) {
            return back()
                ->with('status', 'pending_activation')
                ->with('member_name', $member->first_name);
        }

        // 3. Duplicate check
        $alreadyMarked = Attendance::where('member_id', $member->id)
            ->whereDate('attendance_date', $today)
            ->exists();

        if ($alreadyMarked) {
            return back()
                ->with('status', 'duplicate')
                ->with('member_name', $member->first_name);
        }

        // 4. Record attendance
        try {
            Attendance::create([
                'member_id'       => $member->id,
                'phone'           => $member->phone ?? null,
                'email'           => $member->email ?? null,
                'attendance_date' => $today,
                'submitted_at'    => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Attendance insert failed: ' . $e->getMessage());
            return back()->with('status', 'error');
        }

        return back()
            ->with('status', 'success')
            ->with('member_name', $member->first_name);
    }
}
