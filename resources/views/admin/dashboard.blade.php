@extends('layouts.admin')
@section('title', 'Dashboard')

@section('main')
<div style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:28px;">
    <div>
        <span style="font-size:12px; letter-spacing:0.12em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">Overview</span>
        <h2 class="cg" style="font-size:32px; margin:4px 0 0; color:#1E1208; font-weight:500;">Attendance Dashboard</h2>
    </div>
    <form method="GET" action="{{ route('admin.dashboard') }}" style="display:flex; gap:12px; align-items:flex-end;">
        <div>
            <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; font-family:'Jost',sans-serif;">Date</label>
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                style="width:160px; border:1px solid #FAD9B5; border-radius:8px; padding:8px 12px; font-size:13px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
        </div>
    </form>
</div>

{{-- Stat cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
    @php
        $stats = [
            ['label'=>'Present Today',   'value'=>$todayAttendance->count(), 'bar'=>$rate,  'barColor'=>'#065f46', 'barBg'=>'#d1fae5', 'big'=>true,  'icon'=>'✦'],
            ['label'=>'Total Members',   'value'=>$totalMembers,              'bar'=>100,    'barColor'=>'#1e40af', 'barBg'=>'#dbeafe', 'big'=>true,  'icon'=>'⊙'],
            ['label'=>'Attendance Rate', 'value'=>$rate.'%',                  'bar'=>$rate,  'barColor'=>'#C45E08', 'barBg'=>'#FEE9CF', 'big'=>true,  'icon'=>'◈'],
            ['label'=>'Sunday Service',  'value'=>\Carbon\Carbon::parse($date)->format('d M Y'), 'bar'=>100, 'barColor'=>'#5b21b6', 'barBg'=>'#ede9fe', 'big'=>false, 'icon'=>'⊞'],
        ];
    @endphp
    @foreach($stats as $s)
    <div style="background:#fff; border:1px solid #FAD9B5; border-radius:12px; padding:20px 22px; position:relative; overflow:hidden;">
        <div style="position:absolute; top:14px; right:16px; font-size:20px; color:#FAD9B5;">{{ $s['icon'] }}</div>
        <p style="margin:0 0 6px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">{{ $s['label'] }}</p>
        <p class="cg" style="margin:0; font-size:{{ $s['big'] ? '36px' : '20px' }}; font-weight:600; color:#1E1208; line-height:1.1;">{{ $s['value'] }}</p>
        <div style="margin-top:12px; height:3px; border-radius:2px; background:{{ $s['barBg'] }};">
            <div style="height:3px; border-radius:2px; width:{{ min(100,$s['bar']) }}%; background:{{ $s['barColor'] }}; transition:width 0.6s ease;"></div>
        </div>
    </div>
    @endforeach
</div>

{{-- Second row: weekly chart + group breakdown --}}
<div style="display:grid; grid-template-columns:1.6fr 1fr; gap:20px; margin-bottom:24px;">

    {{-- Weekly Trend: last 7 Sundays --}}
    <div style="background:#fff; border:1px solid #FAD9B5; border-radius:12px; padding:22px 24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
                <p style="margin:0 0 2px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">Last 7 Sundays</p>
                <h3 class="cg" style="margin:0; font-size:20px; color:#1E1208; font-weight:500;">Weekly Trend</h3>
            </div>
            <span style="font-size:11px; color:#F0A055; font-family:'Jost',sans-serif; background:#FEE9CF; padding:3px 10px; border-radius:20px;">Attendance Count</span>
        </div>

        @php
            // Snap to the most recent Sunday on or before $date
            // Carbon::SUNDAY = 0, so startOfWeek(0) anchors to Sunday
            $anchor = \Carbon\Carbon::parse($date)->startOfWeek(\Carbon\Carbon::SUNDAY);

            $weeks = [];
            for ($i = 6; $i >= 0; $i--) {
                $sunday = $anchor->copy()->subWeeks($i);
                $count  = \App\Models\Attendance::whereDate('attendance_date', $sunday->toDateString())->count();
                $weeks[] = [
                    'label' => $sunday->format('d M'),
                    'count' => $count,
                    'isCurrent' => $i === 0, // highlight the most recent Sunday
                ];
            }
            $maxWeek = max(array_column($weeks, 'count')) ?: 1;
        @endphp

        <div style="display:flex; align-items:flex-end; gap:10px; height:100px;">
            @foreach($weeks as $w)
            @php $h = max(4, round(($w['count'] / $maxWeek) * 90)); @endphp
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px;">
                <span style="font-size:10px; color:#C45E08; font-family:'Jost',sans-serif; font-weight:500;">
                    {{ $w['count'] ?: '' }}
                </span>
                <div style="width:100%; background:#FEE9CF; border-radius:4px 4px 0 0; height:{{ $h }}px; position:relative; overflow:hidden;">
                    <div style="position:absolute; bottom:0; left:0; right:0; height:100%;
                        background:{{ $w['isCurrent'] ? 'linear-gradient(180deg,#F0A055,#C45E08)' : 'linear-gradient(180deg,#FAD9B5,#E8A96A)' }};
                        border-radius:4px 4px 0 0; opacity:{{ $w['isCurrent'] ? '1' : '0.55' }};"></div>
                </div>
                <span style="font-size:9px; color:{{ $w['isCurrent'] ? '#C45E08' : '#B86A1A' }}; font-family:'Jost',sans-serif; text-align:center; line-height:1.2; font-weight:{{ $w['isCurrent'] ? '600' : '400' }};">
                    {{ $w['label'] }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Group breakdown --}}
    <div style="background:#fff; border:1px solid #FAD9B5; border-radius:12px; padding:22px 24px;">
        <p style="margin:0 0 2px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">Today</p>
        <h3 class="cg" style="margin:0 0 18px; font-size:20px; color:#1E1208; font-weight:500;">By Group</h3>
        @php
            $groupStats = $todayAttendance->filter(fn($a) => $a->member)->groupBy(fn($a) => $a->member->group ?? 'Unknown')
                ->map(fn($g) => $g->count())->sortDesc();
            $totalToday = $todayAttendance->count() ?: 1;
            $gColors = ['Women'=>'#f9a8d4','Men'=>'#93c5fd','Youth'=>'#86efac','Choir'=>'#fdba74'];
        @endphp
        @if($groupStats->isEmpty())
            <p style="font-size:13px; color:#F0A055; font-family:'Jost',sans-serif;">No data yet.</p>
        @endif
        @foreach($groupStats as $grp => $cnt)
        @php $pct = round(($cnt / $totalToday) * 100); @endphp
        <div style="margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span style="font-size:12px; color:#1E1208; font-family:'Jost',sans-serif;">{{ $grp }}</span>
                <span style="font-size:12px; color:#C45E08; font-weight:500; font-family:'Jost',sans-serif;">{{ $cnt }} <span style="color:#B86A1A; font-weight:400;">({{ $pct }}%)</span></span>
            </div>
            <div style="background:#FEE9CF; border-radius:4px; height:7px;">
                <div style="background:{{ $gColors[$grp] ?? '#F0A055' }}; border-radius:4px; height:7px; width:{{ $pct }}%;"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Third row: attendee list + quick insights --}}
<div style="display:grid; grid-template-columns:1fr 320px; gap:20px; margin-bottom:24px;">

    {{-- Attendee list --}}
    <div style="background:#fff; border:1px solid #FAD9B5; border-radius:12px; overflow:hidden;">
        <div style="padding:18px 24px; border-bottom:1px solid #FAD9B5; display:flex; justify-content:space-between; align-items:center;">
            <h3 class="cg" style="margin:0; font-size:20px; color:#1E1208; font-weight:500;">
                Attendees — {{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}
            </h3>
            <span style="display:inline-block; padding:2px 12px; border-radius:20px; font-size:11px; background:#FEE9CF; color:#C45E08; font-family:'Jost',sans-serif; font-weight:500;">
                {{ $todayAttendance->count() }} present
            </span>
        </div>
        <div style="max-height:360px; overflow-y:auto;">
            @php
                $bgs        = ['#dbeafe','#fce7f3','#d1fae5','#fef3c7','#ede9fe','#fee2e2','#e0f2fe','#fef9c3'];
                $texts      = ['#1e40af','#9d174d','#065f46','#92400e','#5b21b6','#991b1b','#0369a1','#78350f'];
                $groupColors = ['Women'=>['bg'=>'#fff0e8','text'=>'#9d174d'],'Men'=>['bg'=>'#eff6ff','text'=>'#1e40af'],'Youth'=>['bg'=>'#f0fdf4','text'=>'#166534'],'Choir'=>['bg'=>'#fff7ed','text'=>'#9a3412']];
            @endphp
            @forelse($todayAttendance as $a)
            @php $m = $a->member; $idx = ($a->member_id ?? 0) % 8; @endphp
            <div style="display:flex; align-items:center; gap:14px; padding:11px 24px; border-bottom:1px solid #FDF0DC; transition:background 0.15s;"
                 onmouseover="this.style.background='#FFF8EE'" onmouseout="this.style.background='transparent'">
                <div style="width:36px; height:36px; border-radius:50%; background:{{ $bgs[$idx] }}; color:{{ $texts[$idx] }}; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:500; flex-shrink:0; font-family:'Jost',sans-serif;">
                    {{ $m ? strtoupper(substr($m->first_name,0,1).substr($m->last_name,0,1)) : '?' }}
                </div>
                <div style="flex:1; min-width:0;">
                    <p style="margin:0; font-size:13px; font-weight:500; color:#1E1208; font-family:'Jost',sans-serif; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $m ? $m->first_name.' '.$m->last_name : 'Unknown' }}
                    </p>
                    <p style="margin:0; font-size:11px; color:#B86A1A; font-family:'Jost',sans-serif;">{{ $a->email }}</p>
                </div>
                @if($m)
                <span style="display:inline-block; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:500; font-family:'Jost',sans-serif; white-space:nowrap; background:{{ $groupColors[$m->group]['bg'] ?? '#FEE9CF' }}; color:{{ $groupColors[$m->group]['text'] ?? '#C45E08' }};">{{ $m->group }}</span>
                @endif
                <p style="margin:0; font-size:11px; color:#F0A055; font-family:'Jost',sans-serif; white-space:nowrap; flex-shrink:0;">
                    {{ $a->submitted_at ? $a->submitted_at->format('h:i A') : '—' }}
                </p>
            </div>
            @empty
            <div style="padding:40px; text-align:center; font-size:14px; color:#F0A055; font-family:'Jost',sans-serif;">
                No attendance records for this date.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Quick insights panel --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- First check-in --}}
        @php $first = $todayAttendance->sortBy('submitted_at')->first(); @endphp
        <div style="background:#fff; border:1px solid #FAD9B5; border-radius:12px; padding:18px 20px;">
            <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">First Check-in</p>
            @if($first)
            <p class="cg" style="margin:0; font-size:22px; color:#1E1208; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                {{ $first->member ? $first->member->first_name : 'Unknown' }}
            </p>
            <p style="margin:2px 0 0; font-size:12px; color:#F0A055; font-family:'Jost',sans-serif;">{{ $first->submitted_at?->format('h:i A') }}</p>
            @else
            <p style="margin:0; font-size:13px; color:#F0A055; font-family:'Jost',sans-serif;">No check-ins yet</p>
            @endif
        </div>

        {{-- Absent count --}}
        @php $absentCount = $totalMembers - $todayAttendance->count(); @endphp
        <div style="background:#fff5f5; border:1px solid #fecaca; border-radius:12px; padding:18px 20px;">
            <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#991b1b; font-family:'Jost',sans-serif;">Absent Today</p>
            <p class="cg" style="margin:0; font-size:36px; font-weight:600; color:#991b1b;">{{ max(0,$absentCount) }}</p>
            <p style="margin:2px 0 0; font-size:12px; color:#f87171; font-family:'Jost',sans-serif;">out of {{ $totalMembers }} members</p>
        </div>

        {{-- Most active group today --}}
        @php $topGroup = $groupStats->keys()->first() ?? '—'; $topGroupCount = $groupStats->first() ?? 0; @endphp
        <div style="background:#fff; border:1px solid #FAD9B5; border-radius:12px; padding:18px 20px;">
            <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">Top Group Today</p>
            <p class="cg" style="margin:0; font-size:24px; color:#1E1208; font-weight:500;">{{ $topGroup }}</p>
            <p style="margin:2px 0 0; font-size:12px; color:#F0A055; font-family:'Jost',sans-serif;">{{ $topGroupCount }} members present</p>
        </div>

        {{-- Unique this week (Sunday–Saturday) --}}
        @php
            // Week runs Sunday → Saturday
            $weekStart = \Carbon\Carbon::parse($date)->startOfWeek(\Carbon\Carbon::SUNDAY);
            $weekEnd   = $weekStart->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);

            $newThisWeek = \App\Models\Attendance::whereDate('attendance_date', '>=', $weekStart->toDateString())
                ->whereDate('attendance_date', '<=', $weekEnd->toDateString())
                ->distinct('email')
                ->count('email');
        @endphp
        <div style="background:#f0fdf4; border:1px solid #86efac; border-radius:12px; padding:18px 20px;">
            <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#166534; font-family:'Jost',sans-serif;">Unique This Week</p>
            <p class="cg" style="margin:0; font-size:36px; font-weight:600; color:#166534;">{{ $newThisWeek }}</p>
            <p style="margin:2px 0 0; font-size:12px; color:#4ade80; font-family:'Jost',sans-serif;">distinct attendees</p>
        </div>
    </div>
</div>
@endsection
