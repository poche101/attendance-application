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

        if ($group = $request->get('group')) {
            $query->where('group', 'like', "%$group%");
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

        // Fetch today's records matching via BOTH tracking formats (id and email) for absolute safety
        $todayAttendance = Attendance::whereDate('attendance_date', now()->toDateString())->get();

        $todayMemberIds = $todayAttendance->pluck('member_id')->filter()->toArray();
        $todayEmails = $todayAttendance->pluck('email')
            ->map(fn($e) => strtolower(trim($e)))
            ->filter()
            ->toArray();

        // Filter by present/absent status using custom matrix tracking fallback loops
        if ($request->get('status') === 'present') {
            $query->where(function ($q) use ($todayMemberIds, $todayEmails) {
                $q->whereIn('id', $todayMemberIds);
                if (!empty($todayEmails)) {
                    $q->orWhereIn(DB::raw('LOWER(email)'), $todayEmails);
                }
            });

            // If absolutely nobody was found present, force empty results block smoothly
            if (empty($todayMemberIds) && empty($todayEmails)) {
                $query->whereRaw('1 = 0');
            }
        } elseif ($request->get('status') === 'absent') {
            if (!empty($todayMemberIds)) {
                $query->whereNotIn('id', $todayMemberIds);
            }
            if (!empty($todayEmails)) {
                $query->whereNotIn(DB::raw('LOWER(email)'), $todayEmails);
            }
        }

        $sortCol = in_array($request->get('sort'), ['first_name','last_name','email','group','church','cell'])
            ? $request->get('sort') : 'first_name';
        $sortDir = $request->get('dir') === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortCol, $sortDir);

        $members = $query->paginate(8)->withQueryString();

        // Fetch counts accurately regardless of database schema style mappings
        $memberIds = $members->pluck('id');
        $memberEmails = $members->pluck('email')->map(fn($e) => strtolower(trim($e)))->toArray();

        $memberAttCounts = Attendance::whereIn('member_id', $memberIds)
            ->orWhereIn(DB::raw('LOWER(email)'), $memberEmails)
            ->selectRaw('member_id, email, COUNT(*) as cnt')
            ->groupBy('member_id', 'email')
            ->get()
            ->reduce(function ($carry, $item) use ($members) {
                // Map counters to the explicit ID key context your Blade template targets
                $matchedMember = $members->first(function($m) use ($item) {
                    return $m->id == $item->member_id || strtolower(trim($m->email)) === strtolower(trim($item->email));
                });

                if ($matchedMember) {
                    $carry[$matchedMember->id] = ($carry[$matchedMember->id] ?? 0) + $item->cnt;
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
            'group'      => 'nullable|string|max:100',
            'church'     => 'nullable|string|max:150',
            'cell'       => 'nullable|string|max:150',
            'birthday'   => 'nullable|date',
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
            'group'      => 'nullable|string|max:100',
            'church'     => 'nullable|string|max:150',
            'cell'       => 'nullable|string|max:150',
            'birthday'   => 'nullable|date',
        ]);

        $member->update($data);
        return back()->with('toast', 'Member updated.');
    }

    public function deactivate(Member $member)
    {
        $member->update(['is_active' => false]);
        return back()->with('toast', 'Member deactivated.');
    }
}
