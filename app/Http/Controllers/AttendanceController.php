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
            'phone' => 'required|string|max:20',
        ]);

        // Normalize: strip non-digits, handle country code, ensure leading 0
        $raw    = trim($request->input('phone'));
        $digits = preg_replace('/\D/', '', $raw);
        if (str_starts_with($digits, '234') && strlen($digits) > 10) {
            $digits = substr($digits, 3);
        }
        if (!str_starts_with($digits, '0')) {
            $digits = '0' . $digits;
        }
        $phone = $digits;

        $today = now()->toDateString();

        // 1. Look up member by phone number
        $member = Member::where('phone', $phone)->first();

        // 2. Member entirely missing — send them to register
        if (!$member) {
            return back()
                ->with('status', 'not_found')
                ->with('attempted_phone', $phone);
        }

        // 3. Strict duplicate check — only one check-in per day
        $alreadyMarked = Attendance::where('member_id', $member->id)
            ->whereDate('attendance_date', $today)
            ->exists();

        if ($alreadyMarked) {
            return back()
                ->with('status', 'duplicate')
                ->with('member_name', $member->first_name);
        }

        // 4. All clear — record attendance
        try {
            Attendance::create([
                'member_id'       => $member->id,
                'phone'           => $phone,
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