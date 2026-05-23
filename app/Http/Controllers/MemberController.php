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

        // Filter by attendance date range
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        if ($dateFrom || $dateTo) {
            $query->whereHas('attendances', function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom) $q->whereDate('attendance_date', '>=', $dateFrom);
                if ($dateTo)   $q->whereDate('attendance_date', '<=', $dateTo);
            });
        }

        // Fetch today's attendance records
        $todayAttendance = Attendance::whereDate('attendance_date', now()->toDateString())->get();
        $todayMemberIds  = $todayAttendance->pluck('member_id')->filter()->toArray();
        $todayEmails     = $todayAttendance->pluck('email')
            ->map(fn($e) => strtolower(trim($e)))
            ->filter()
            ->toArray();

        // Status filter
        if ($request->get('status') === 'present') {
            if (empty($todayMemberIds) && empty($todayEmails)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($q) use ($todayMemberIds, $todayEmails) {
                    if (!empty($todayMemberIds)) {
                        $q->whereIn('id', $todayMemberIds);
                    }
                    if (!empty($todayEmails)) {
                        $q->orWhereIn(DB::raw('LOWER(email)'), $todayEmails);
                    }
                });
            }
        } elseif ($request->get('status') === 'absent') {
            if (!empty($todayMemberIds)) {
                $query->whereNotIn('id', $todayMemberIds);
            }
            if (!empty($todayEmails)) {
                $query->whereNotIn(DB::raw('LOWER(email)'), $todayEmails);
            }
        }

        // Sorting — include title now
        $sortCol = in_array($request->get('sort'), ['title', 'first_name', 'last_name', 'email', 'cell', 'church'])
            ? $request->get('sort') : 'first_name';
        $sortDir = $request->get('dir') === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortCol, $sortDir);

        $members = $query->paginate(8)->withQueryString();

        // Attendance counts for displayed members
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:members,email',
            'phone'      => 'nullable|string|max:30',
            'church'     => 'nullable|string|max:150',
            'cell'       => 'nullable|string|max:150',
        ]);

        Member::create($data + ['is_active' => true]);
        return back()->with('toast', 'Member added successfully.');
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'title'      => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:members,email,' . $member->id,
            'phone'      => 'nullable|string|max:30',
            'church'     => 'nullable|string|max:150',
            'cell'       => 'nullable|string|max:150',
        ]);

        $member->update($data);
        return back()->with('toast', 'Member updated.');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return back()->with('toast', 'Member deleted successfully.');
    }
}
