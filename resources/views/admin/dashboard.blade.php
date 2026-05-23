@extends('layouts.admin')
@section('title', 'Dashboard')

@section('main')
{{-- Responsive CSS Styles injected directly into the view --}}
<style>
    .dashboard-wrapper {
        padding: 16px;
        font-family: 'DM Sans', sans-serif;
    }
    .dashboard-header {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 28px;
    }
    .stat-cards-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }
    .two-column-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    .split-layout-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    .insights-panel {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    /* Small Tablets and up */
    @media (min-width: 640px) {
        .dashboard-header {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }
        .stat-cards-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .insights-panel {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Medium/Large Tablets and up */
    @media (min-width: 768px) {
        .two-column-grid {
            grid-template-columns: 1.6fr 1fr;
        }
        .split-layout-grid {
            grid-template-columns: 1fr 320px;
        }
        .insights-panel {
            display: flex;
            flex-direction: column;
        }
    }

    /* Laptops and Desktops */
    @media (min-width: 1024px) {
        .stat-cards-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>

<div class="dashboard-wrapper">
    <div class="dashboard-header">
        <div>
            <span style="font-size:12px; letter-spacing:0.12em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">Overview</span>
            <h2 class="font-head" style="font-size:32px; margin:4px 0 0; color:#0F172A; font-weight:600;">Attendance Dashboard</h2>
        </div>
        <form method="GET" action="{{ route('admin.dashboard') }}" style="display:flex; gap:12px; align-items:flex-end; width: 100%; max-width: 240px; margin-top: 4px;">
            <div style="width: 100%;">
                <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif;">Date</label>
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                    style="width:100%; box-sizing:border-box; border:1.5px solid #93C5FD; border-radius:8px; padding:8px 12px; font-size:13px; background:white; color:#0F172A; font-family:'DM Sans',sans-serif;">
            </div>
        </form>
    </div>

    {{-- Stat cards --}}
    <div class="stat-cards-grid">
        @php
            $stats = [
                ['label'=>'Present Today',   'value'=>$todayAttendance->count(), 'bar'=>$rate,  'barColor'=>'#166534', 'barBg'=>'#D1FAE5', 'big'=>true,  'icon'=>'✦'],
                ['label'=>'Total Members',   'value'=>$totalMembers,              'bar'=>100,    'barColor'=>'#1E40AF', 'barBg'=>'#DBEAFE', 'big'=>true,  'icon'=>'👥'],
                ['label'=>'Attendance Rate', 'value'=>$rate.'%',                  'bar'=>$rate,  'barColor'=>'#1E40AF', 'barBg'=>'#DBEAFE', 'big'=>true,  'icon'=>'📈'],
                ['label'=>'Sunday Service',  'value'=>\Carbon\Carbon::parse($date)->format('d M Y'), 'bar'=>100, 'barColor'=>'#1E40AF', 'barBg'=>'#DBEAFE', 'big'=>false, 'icon'=>'📅'],
            ];
        @endphp
        @foreach($stats as $s)
        <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:12px; padding:20px 22px; position:relative; overflow:hidden;">
            <div style="position:absolute; top:14px; right:16px; font-size:20px; color:#DBEAFE;">{{ $s['icon'] }}</div>
            <p style="margin:0 0 6px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">{{ $s['label'] }}</p>
            <p class="font-head" style="margin:0; font-size:{{ $s['big'] ? '36px' : '22px' }}; font-weight:700; color:#0F172A; line-height:1.1;">{{ $s['value'] }}</p>
            <div style="margin-top:12px; height:4px; border-radius:4px; background:{{ $s['barBg'] }};">
                <div style="height:4px; border-radius:4px; width:{{ min(100,$s['bar']) }}%; background:{{ $s['barColor'] }}; transition:width 0.6s ease;"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Second row: weekly chart + group breakdown --}}
    <div class="two-column-grid">

        {{-- Weekly Trend: last 7 Sundays --}}
        <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:12px; padding:22px 24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap: 8px;">
                <div>
                    <p style="margin:0 0 2px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">Last 7 Sundays</p>
                    <h3 class="font-head" style="margin:0; font-size:20px; color:#0F172A; font-weight:600;">Weekly Trend</h3>
                </div>
                <span style="font-size:11px; color:#1E40AF; font-family:'DM Sans',sans-serif; background:#DBEAFE; padding:3px 10px; border-radius:20px; white-space:nowrap;">Attendance Count</span>
            </div>

            @php
                $anchor = \Carbon\Carbon::parse($date)->startOfWeek(\Carbon\Carbon::SUNDAY);
                $weeks = [];
                for ($i = 6; $i >= 0; $i--) {
                    $sunday = $anchor->copy()->subWeeks($i);
                    $count  = \App\Models\Attendance::whereDate('attendance_date', $sunday->toDateString())->count();
                    $weeks[] = [
                        'label' => $sunday->format('d M'),
                        'count' => $count,
                        'isCurrent' => $i === 0,
                    ];
                }
                $maxWeek = max(array_column($weeks, 'count')) ?: 1;
            @endphp

            <div style="display:flex; align-items:flex-end; gap:8px; height:120px; overflow-x:auto; padding-bottom:4px;">
                @foreach($weeks as $w)
                @php $h = max(4, round(($w['count'] / $maxWeek) * 90)); @endphp
                <div style="flex:1; min-width:35px; display:flex; flex-direction:column; align-items:center; gap:6px;">
                    <span style="font-size:10px; color:#1E40AF; font-family:'DM Sans',sans-serif; font-weight:600;">
                        {{ $w['count'] ?: '' }}
                    </span>
                    <div style="width:100%; background:#DBEAFE; border-radius:4px 4px 0 0; height:{{ $h }}px; position:relative; overflow:hidden;">
                        <div style="position:absolute; bottom:0; left:0; right:0; height:100%;
                            background:{{ $w['isCurrent'] ? '#1E40AF' : '#60A5FA' }};
                            border-radius:4px 4px 0 0; opacity:{{ $w['isCurrent'] ? '1' : '0.75' }};"></div>
                    </div>
                    <span style="font-size:9px; color:{{ $w['isCurrent'] ? '#1E40AF' : '#64748B' }}; font-family:'DM Sans',sans-serif; text-align:center; line-height:1.2; font-weight:{{ $w['isCurrent'] ? '600' : '400' }}; white-space:nowrap;">
                        {{ $w['label'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Church breakdown --}}
        <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:12px; padding:22px 24px;">
            <p style="margin:0 0 2px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">Today</p>
            <h3 class="font-head" style="margin:0 0 18px; font-size:20px; color:#0F172A; font-weight:600;">By Church</h3>
            @php
                $churchStats = $todayAttendance
                    ->filter(fn($a) => $a->member)
                    ->groupBy(fn($a) => $a->member->church ?? 'Unknown')
                    ->map(fn($g) => $g->count())
                    ->sortDesc();
                $totalToday = $todayAttendance->count() ?: 1;
                $cColors = ['#3B82F6','#60A5FA','#34D399','#F59E0B','#A78BFA','#F472B6','#38BDF8','#FB923C'];
            @endphp
            @if($churchStats->isEmpty())
                <p style="font-size:13px; color:#64748B; font-family:'DM Sans',sans-serif;">No data yet.</p>
            @endif
            @foreach($churchStats as $church => $cnt)
            @php
                $pct      = round(($cnt / $totalToday) * 100);
                $colorIdx = $loop->index % count($cColors);
            @endphp
            <div style="margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:4px; gap:8px;">
                    <span style="font-size:12px; color:#0F172A; font-family:'DM Sans',sans-serif; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">{{ $church }}</span>
                    <span style="font-size:12px; color:#1E40AF; font-weight:600; font-family:'DM Sans',sans-serif; flex-shrink:0;">
                        {{ $cnt }} <span style="color:#64748B; font-weight:400;">({{ $pct }}%)</span>
                    </span>
                </div>
                <div style="background:#DBEAFE; border-radius:4px; height:7px;">
                    <div style="background:{{ $cColors[$colorIdx] }}; border-radius:4px; height:7px; width:{{ $pct }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Third row: attendee list + quick insights --}}
    <div class="split-layout-grid">

        {{-- Attendee list --}}
        <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:12px; overflow:hidden;">
            <div style="padding:18px 24px; border-bottom:1px solid #E2E8F0; display:flex; justify-content:space-between; align-items:center; gap:8px;">
                <h3 class="font-head" style="margin:0; font-size:20px; color:#0F172A; font-weight:600; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">
                    Attendees — {{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}
                </h3>
                <span style="display:inline-block; padding:2px 12px; border-radius:20px; font-size:11px; background:#DBEAFE; color:#1E40AF; font-family:'DM Sans',sans-serif; font-weight:500; white-space:nowrap;">
                    {{ $todayAttendance->count() }} present
                </span>
            </div>
            <div style="max-height:360px; overflow-y:auto;">
                @php
                    $bgs   = ['#DBEAFE','#BFDBFE','#BAE6FD','#A5F3FC','#C4D0FF','#E0E7FF','#DBEAFE','#BFDBFE'];
                    $texts = ['#1E40AF','#1E3A8A','#0C4A6E','#164E63','#312E81','#1E40AF','#1E3A8A','#0C4A6E'];
                    $churchBadgeColors = [
                        ['bg'=>'#DBEAFE','text'=>'#1E40AF'],
                        ['bg'=>'#D1FAE5','text'=>'#166534'],
                        ['bg'=>'#FEF3C7','text'=>'#92400E'],
                        ['bg'=>'#F3E8FF','text'=>'#6B21A8'],
                        ['bg'=>'#FFE4E6','text'=>'#9F1239'],
                        ['bg'=>'#E0F2FE','text'=>'#0369A1'],
                        ['bg'=>'#FEE2E2','text'=>'#991B1B'],
                        ['bg'=>'#ECFDF5','text'=>'#065F46'],
                    ];
                    // Build a stable church→color index map for consistent badge colours
                    $churchColorMap = [];
                    $colorCursor    = 0;
                @endphp
                @forelse($todayAttendance as $a)
                @php
                    $m   = $a->member;
                    $idx = ($a->member_id ?? 0) % 8;
                    $ch  = $m->church ?? null;
                    if ($ch && !isset($churchColorMap[$ch])) {
                        $churchColorMap[$ch] = $colorCursor % count($churchBadgeColors);
                        $colorCursor++;
                    }
                    $badgeStyle = $ch ? $churchBadgeColors[$churchColorMap[$ch]] : ['bg'=>'#F1F5F9','text'=>'#475569'];
                @endphp
                <div style="display:flex; align-items:center; gap:14px; padding:11px 24px; border-bottom:1px solid #F1F5F9; transition:background 0.15s;"
                     onmouseover="this.style.background='#F0F7FF'" onmouseout="this.style.background='transparent'">
                    <div style="width:36px; height:36px; border-radius:50%; background:{{ $bgs[$idx] }}; color:{{ $texts[$idx] }}; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:500; flex-shrink:0;">
                        {{ $m ? strtoupper(substr($m->first_name,0,1).substr($m->last_name,0,1)) : '?' }}
                    </div>
                    <div style="flex:1; min-width:0;">
                        <p style="margin:0; font-size:13px; font-weight:500; color:#0F172A; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $m ? $m->first_name.' '.$m->last_name : 'Unknown' }}
                        </p>
                        <p style="margin:0; font-size:11px; color:#64748B; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $a->email }}</p>
                    </div>
                    @if($ch)
                    <span style="display:inline-block; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:500; white-space:nowrap; background:{{ $badgeStyle['bg'] }}; color:{{ $badgeStyle['text'] }}; max-width:80px; overflow:hidden; text-overflow:ellipsis;">
                        {{ $ch }}
                    </span>
                    @endif
                    <p style="margin:0; font-size:11px; color:#3B82F6; white-space:nowrap; flex-shrink:0;">
                        {{ $a->submitted_at ? $a->submitted_at->format('h:i A') : '—' }}
                    </p>
                </div>
                @empty
                <div style="padding:40px; text-align:center; font-size:14px; color:#64748B; font-family:'DM Sans',sans-serif;">
                    No attendance records for this date.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Quick insights panel --}}
        <div class="insights-panel">

            {{-- First check-in --}}
            @php $first = $todayAttendance->sortBy('submitted_at')->first(); @endphp
            <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:12px; padding:18px 20px;">
                <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">First Check-in</p>
                @if($first)
                <p class="font-head" style="margin:0; font-size:22px; color:#0F172A; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $first->member ? $first->member->first_name : 'Unknown' }}
                </p>
                <p style="margin:2px 0 0; font-size:12px; color:#3B82F6; font-family:'DM Sans',sans-serif;">{{ $first->submitted_at?->format('h:i A') }}</p>
                @else
                <p style="margin:0; font-size:13px; color:#64748B; font-family:'DM Sans',sans-serif;">No check-ins yet</p>
                @endif
            </div>

            {{-- Absent count --}}
            @php $absentCount = $totalMembers - $todayAttendance->count(); @endphp
            <div style="background:#FEF2F2; border:1.5px solid #FECACA; border-radius:12px; padding:18px 20px;">
                <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#991B1B; font-family:'DM Sans',sans-serif;">Absent Today</p>
                <p class="font-head" style="margin:0; font-size:36px; font-weight:700; color:#991B1B;">{{ max(0,$absentCount) }}</p>
                <p style="margin:2px 0 0; font-size:12px; color:#EF4444; font-family:'DM Sans',sans-serif;">out of {{ $totalMembers }} members</p>
            </div>

            {{-- Top Church Today --}}
            @php
                $topChurch      = $churchStats->keys()->first() ?? '—';
                $topChurchCount = $churchStats->first() ?? 0;
                $topChurchPct   = $totalToday > 0 ? round(($topChurchCount / $totalToday) * 100) : 0;
            @endphp
            <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:12px; padding:18px 20px;">
                <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">Top Church Today</p>
                <p class="font-head" style="margin:0; font-size:22px; color:#0F172A; font-weight:600; line-height:1.2; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    {{ $topChurch }}
                </p>
                <p style="margin:4px 0 0; font-size:12px; color:#3B82F6; font-family:'DM Sans',sans-serif;">
                    {{ $topChurchCount }} members · {{ $topChurchPct }}% of today
                </p>
            </div>

            {{-- Unique this week --}}
            @php
                $weekStart   = \Carbon\Carbon::parse($date)->startOfWeek(\Carbon\Carbon::SUNDAY);
                $weekEnd     = $weekStart->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                $newThisWeek = \App\Models\Attendance::whereDate('attendance_date', '>=', $weekStart->toDateString())
                    ->whereDate('attendance_date', '<=', $weekEnd->toDateString())
                    ->distinct('email')
                    ->count('email');
            @endphp
            <div style="background:#F0FDF4; border:1.5px solid #86EFAC; border-radius:12px; padding:18px 20px;">
                <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#166534; font-family:'DM Sans',sans-serif;">Unique This Week</p>
                <p class="font-head" style="margin:0; font-size:36px; font-weight:700; color:#166534;">{{ $newThisWeek }}</p>
                <p style="margin:2px 0 0; font-size:12px; color:#4ADE80; font-family:'DM Sans',sans-serif;">distinct attendees</p>
            </div>
        </div>
    </div>
</div>
@endsection
