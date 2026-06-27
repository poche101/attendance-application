<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Attendance;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Normalize a phone number to a consistent format.
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        // Strip country code prefix (234 for Nigeria)
        if (str_starts_with($digits, '234') && strlen($digits) > 10) {
            $digits = substr($digits, 3);
        }

        // Ensure leading zero
        if (!str_starts_with($digits, '0')) {
            $digits = '0' . $digits;
        }

        return $digits;
    }

    public function index(Request $request)
    {
        $query = Member::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name',  'like', "%$search%")
                  ->orWhere('phone',      'like', "%$search%")
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

        $todayMemberIds = Attendance::whereDate('attendance_date', now()->toDateString())
            ->pluck('member_id')
            ->filter()
            ->toArray();

        if ($request->get('status') === 'present') {
            if (empty($todayMemberIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('id', $todayMemberIds);
            }
        } elseif ($request->get('status') === 'absent') {
            if (!empty($todayMemberIds)) {
                $query->whereNotIn('id', $todayMemberIds);
            }
        }

        $sortCol = in_array($request->get('sort'), ['title', 'first_name', 'last_name', 'phone', 'email', 'cell', 'church'])
            ? $request->get('sort') : 'first_name';
        $sortDir = $request->get('dir') === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortCol, $sortDir);

        $members = $query->paginate(8)->withQueryString();

        $memberIds = $members->pluck('id')->filter()->toArray();

        $memberAttCounts = Attendance::whereIn('member_id', $memberIds)
            ->selectRaw('member_id, COUNT(*) as cnt')
            ->groupBy('member_id')
            ->pluck('cnt', 'member_id')
            ->toArray();

        $churches = Member::distinct()->pluck('church')->filter()->sort()->values();

        return view('admin.members', compact(
            'members', 'todayMemberIds', 'churches', 'sortCol', 'sortDir', 'memberAttCounts'
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
            'email'      => 'nullable|email|max:255|unique:members,email',
            'phone'      => 'nullable|string|max:30|unique:members,phone',
            'group'      => 'nullable|string|max:150',
            'church'     => 'nullable|string|max:150',
            'cell'       => 'nullable|string|max:150',
            'birthday'   => 'nullable|date',
        ]);

        if (!empty($data['phone'])) {
            $data['phone'] = $this->normalizePhone($data['phone']);
        }

        if (!empty($data['email'])) {
            $data['email'] = strtolower(trim($data['email']));
        }

        $data['is_active'] = true;

        Member::create($data);

        return back()->with('toast', 'Member added successfully.');
    }

    /**
     * Public check-in self-registration.
     */
    public function publicStore(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|max:255|unique:members,email',
            'phone'      => 'nullable|string|max:30|unique:members,phone',
            'church'     => 'nullable|string|max:150',
        ]);

        $validated['email'] = strtolower(trim($validated['email']));

        if (!empty($validated['phone'])) {
            $validated['phone'] = $this->normalizePhone($validated['phone']);
        }

        $validated['is_active'] = true; // pending admin approval

        Member::create($validated);

        return redirect()->route('checkin')->with('status', 'registered');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return back()->with('toast', 'Member deleted successfully.');
    }
}
