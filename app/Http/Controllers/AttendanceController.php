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
        $request->validate(['email' => 'required|email']);

        $email = strtolower(trim($request->input('email')));
        $today = now()->toDateString();

        // 1. Look up member strictly by email first
        $member = Member::where('email', $email)->first();

        // 2. Member entirely missing — send them to register
        if (!$member) {
            return back()
                ->with('status', 'not_found')
                ->with('attempted_email', $email);
        }

        // PENDING SCREEN REMOVED: Status check bypassed completely.

        // 3. STRICT DUPLICATE CHECK — Ensure they can only check in ONCE per day
        $alreadyMarked = Attendance::where('email', $email)
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
                'email'           => $email,
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
