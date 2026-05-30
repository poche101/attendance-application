<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name',  'like', "%$search%")
                  ->orWhere('email',      'like', "%$search%");
            });
        }

        if ($cell = $request->get('cell')) {
            $query->where('cell', 'like', "%$cell%");
        }

        if ($church = $request->get('church')) {
            $query->where('church', $church);
        }

        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        if ($dateFrom || $dateTo) {
            $query->whereHas('attendances', function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom) $q->whereDate('attendance_date', '>=', $dateFrom);
                if ($dateTo)   $q->whereDate('attendance_date', '<=', $dateTo);
            });
        }

        $todayAttendance = Attendance::whereDate('attendance_date', now()->toDateString())->get();
        $todayMemberIds  = $todayAttendance->pluck('member_id')->filter()->toArray();
        $todayEmails     = $todayAttendance->pluck('email')
            ->map(fn($e) => strtolower(trim($e)))
            ->filter()
            ->toArray();

        if ($request->get('status') === 'present') {
            if (empty($todayMemberIds) && empty($todayEmails)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($q) use ($todayMemberIds, $todayEmails) {
                    if (!empty($todayMemberIds)) $q->whereIn('id', $todayMemberIds);
                    if (!empty($todayEmails))    $q->orWhereIn(DB::raw('LOWER(email)'), $todayEmails);
                });
            }
        } elseif ($request->get('status') === 'absent') {
            if (!empty($todayMemberIds)) $query->whereNotIn('id', $todayMemberIds);
            if (!empty($todayEmails))    $query->whereNotIn(DB::raw('LOWER(email)'), $todayEmails);
        }

        $sortCol = in_array($request->get('sort'), ['title', 'first_name', 'last_name', 'email', 'cell', 'church'])
            ? $request->get('sort') : 'first_name';
        $sortDir = $request->get('dir') === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortCol, $sortDir);

        $members = $query->paginate(8)->withQueryString();

        $memberIds    = $members->pluck('id')->filter()->toArray();
        $memberEmails = $members->pluck('email')->map(fn($e) => strtolower(trim($e)))->toArray();

        $memberAttCounts = Attendance::where(function ($q) use ($memberIds, $memberEmails) {
                if (!empty($memberIds))    $q->whereIn('member_id', $memberIds);
                if (!empty($memberEmails)) $q->orWhereIn(DB::raw('LOWER(email)'), $memberEmails);
            })
            ->selectRaw('member_id, LOWER(email) as email_lower, COUNT(*) as cnt')
            ->groupBy('member_id', 'email_lower')
            ->get()
            ->reduce(function ($carry, $item) use ($members) {
                $match = $members->first(fn($m) =>
                    ($item->member_id && $m->id == $item->member_id) ||
                    strtolower(trim($m->email)) === $item->email_lower
                );
                if ($match) {
                    $carry[$match->id] = ($carry[$match->id] ?? 0) + $item->cnt;
                }
                return $carry;
            }, []);

        $churches = Member::distinct()->pluck('church')->filter()->sort()->values();

        return view('admin.members', compact(
            'members', 'todayEmails', 'churches', 'sortCol', 'sortDir', 'memberAttCounts'
        ));
    }

    /**
     * Admin panel: add a new member (active immediately).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:members,email',
            'phone'      => 'nullable|string|max:30',
            'group'      => 'nullable|string|max:150',
            'church'     => 'nullable|string|max:150',
            'cell'       => 'nullable|string|max:150',
            'birthday'   => 'nullable|date',
        ]);

        $data['email']     = strtolower(trim($data['email']));
        $data['is_active'] = true;

        Member::create($data);

        return back()->with('toast', 'Member added successfully.');
    }

    /**
     * Public check-in self-registration.
     * On failure: redirect back to checkin with errors + re-flash attempted_email.
     * On success: redirect to checkin with status=registered.
     */
    public function publicStore(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:members,email',
            'phone'      => 'nullable|string|max:30',
            'group'      => 'nullable|string|max:150',
            'church'     => 'nullable|string|max:150',
            'cell'       => 'nullable|string|max:150',
            'birthday'   => 'nullable|date',
        ]);

        // Validation passed — save and redirect with success status
        $validated['email']     = strtolower(trim($validated['email']));
        $validated['is_active'] = false; // pending admin approval

        Member::create($validated);

        return redirect()->route('checkin')
            ->with('status', 'registered');
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:members,email,' . $member->id,
            'phone'      => 'nullable|string|max:30',
            'group'      => 'nullable|string|max:150',
            'church'     => 'nullable|string|max:150',
            'cell'       => 'nullable|string|max:150',
            'birthday'   => 'nullable|date',
        ]);

        $data['email'] = strtolower(trim($data['email']));
        $member->update($data);

        return back()->with('toast', 'Member updated.');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return back()->with('toast', 'Member deleted successfully.');
    }
}
