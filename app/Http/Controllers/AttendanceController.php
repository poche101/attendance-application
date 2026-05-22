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

        // Duplicate check
        $alreadyMarked = Attendance::where('email', $email)
            ->whereDate('attendance_date', $today)
            ->exists();

        if ($alreadyMarked) {
            return back()->with('status', 'duplicate');
        }

        $member = Member::where('email', $email)
            ->where('is_active', true)
            ->first();

        try {
            Attendance::create([
                'member_id'       => $member?->id,
                'email'           => $email,
                'attendance_date' => $today,
                'submitted_at'    => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Attendance insert failed: ' . $e->getMessage());
            return back()->with('status', 'error');
        }

        if (!$member) {
            return back()->with('status', 'not_found');
        }

        return back()
            ->with('status', 'success')
            ->with('member_name', $member->first_name);
    }
}
