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
    .present-absent-columns {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
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
        .present-absent-columns {
            grid-template-columns: 1fr 1fr;
        }
    }

    /* Laptops and Desktops */
    @media (min-width: 1024px) {
        .stat-cards-grid {
            grid-template-columns: repeat(5, 1fr);
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
                <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif;">Date Selection</label>
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                    style="width:100%; box-sizing:border-box; border:1.5px solid #93C5FD; border-radius:8px; padding:8px 12px; font-size:13px; background:white; color:#0F172A; font-family:'DM Sans',sans-serif;">
            </div>
        </form>
    </div>

    {{-- Stat cards --}}
    <div class="stat-cards-grid">
        @php
            $childrenToday = $todayAttendance->sum('children_count');
            $stats = [
                ['label'=>'Present Today',   'value'=>$todayAttendance->count(), 'bar'=>$rate,  'barColor'=>'#166534', 'barBg'=>'#D1FAE5', 'big'=>true,  'icon'=>'✦'],
                ['label'=>'Children Checked In', 'value'=>$childrenToday,        'bar'=>$todayAttendance->count() ? min(100, round(($childrenToday / max(1,$todayAttendance->count())) * 100)) : 0, 'barColor'=>'#9333EA', 'barBg'=>'#F3E8FF', 'big'=>true,  'icon'=>'🧒'],
                ['label'=>'Total Members',   'value'=>$totalMembers,              'bar'=>100,    'barColor'=>'#1E40AF', 'barBg'=>'#DBEAFE', 'big'=>true,  'icon'=>'👥'],
                ['label'=>'Attendance Rate', 'value'=>$rate.'%',                  'bar'=>$rate,  'barColor'=>'#1E40AF', 'barBg'=>'#DBEAFE', 'big'=>true,  'icon'=>'📈'],
                ['label'=>'Selected Date',  'value'=>\Carbon\Carbon::parse($date)->format('d M Y'), 'bar'=>100, 'barColor'=>'#1E40AF', 'barBg'=>'#DBEAFE', 'big'=>false, 'icon'=>'📅'],
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

    {{-- SMS result flash message --}}
    @if(session('sms_status'))
    <div style="margin-bottom:24px; padding:14px 18px; border-radius:10px; font-size:13px; font-family:'DM Sans',sans-serif;
        @if(session('sms_status') === 'sent') background:#F0FDF4; border:1.5px solid #86EFAC; color:#166534;
        @elseif(session('sms_status') === 'none') background:#EFF6FF; border:1.5px solid #93C5FD; color:#1E40AF;
        @else background:#FEF2F2; border:1.5px solid #FECACA; color:#991B1B;
        @endif">
        @if(session('sms_status') === 'sent')
            ✓ SMS sent to {{ session('sms_sent_count') }} {{ session('sms_audience') === 'present' ? 'present' : 'absent' }} member(s).
            @if(session('sms_skipped_count') > 0)
                {{ session('sms_skipped_count') }} skipped (invalid or missing phone number).
            @endif
        @elseif(session('sms_status') === 'none')
            No {{ session('sms_audience') === 'present' ? 'present' : 'absent' }} members with a valid phone number found for this window — nothing was sent.
        @else
            Could not send SMS: {{ session('sms_error') }}
        @endif
    </div>
    @endif

    {{-- Second row: weekly chart + group breakdown --}}
    <div class="two-column-grid">

        {{-- Weekly Trend: last 7 Sundays relative to current selection --}}
        <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:12px; padding:22px 24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap: 8px;">
                <div>
                    <p style="margin:0 0 2px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">Historical Trend</p>
                    <h3 class="font-head" style="margin:0; font-size:20px; color:#0F172A; font-weight:600;">Weekly Attendance</h3>
                </div>
                <span style="font-size:11px; color:#1E40AF; font-family:'DM Sans',sans-serif; background:#DBEAFE; padding:3px 10px; border-radius:20px; white-space:nowrap;">Sundays Only</span>
            </div>

            @php
                // Get the Sunday of the selected date's week
                $anchor = \Carbon\Carbon::parse($date)->startOfWeek(\Carbon\Carbon::SUNDAY);
                $weeks = [];
                // Loop back to show the current Sunday and the 6 preceding ones
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
            <p style="margin:0 0 2px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">Current Selection</p>
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
                <p style="font-size:13px; color:#64748B; font-family:'DM Sans',sans-serif;">No records for this date.</p>
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

    {{-- Present / Absent — rolling {{ $windowDays }}-day window --}}
    <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:12px; padding:22px 24px; margin-bottom:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap:wrap; gap:12px;">
            <div>
                <p style="margin:0 0 2px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">Rolling Window</p>
                <h3 class="font-head" style="margin:0; font-size:20px; color:#0F172A; font-weight:600;">
                    Present &amp; Absent — Last {{ $windowDays }} Days
                </h3>
                <p style="margin:4px 0 0; font-size:11px; color:#64748B; font-family:'DM Sans',sans-serif;">
                    {{ \Carbon\Carbon::parse($windowStart)->format('d M') }} – {{ \Carbon\Carbon::parse($date)->format('d M Y') }}.
                    A member stays "Present" for {{ $windowDays }} days after their last check-in, then moves to "Absent".
                </p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                @if($rollingAttendance->count() > 0)
                <button type="button" onclick="document.getElementById('sms-present-modal').classList.remove('hidden')"
                    style="background:#166534; color:#fff; border:none; border-radius:8px; padding:10px 18px; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; white-space:nowrap;">
                    📲 Send SMS to Present ({{ $rollingAttendance->count() }})
                </button>
                @endif
                @if($absentMembers->count() > 0)
                <button type="button" onclick="document.getElementById('sms-modal').classList.remove('hidden')"
                    style="background:#1E40AF; color:#fff; border:none; border-radius:8px; padding:10px 18px; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; white-space:nowrap;">
                    📲 Send SMS to Absentees ({{ $absentMembers->count() }})
                </button>
                @endif
            </div>
        </div>

        <div class="present-absent-columns">
            {{-- Present column --}}
            <div>
                <p style="margin:0 0 10px; font-size:12px; font-weight:600; color:#166534; font-family:'DM Sans',sans-serif;">
                    ✓ Present ({{ $rollingAttendance->count() }})
                </p>
                <div style="max-height:320px; overflow-y:auto; border:1px solid #E2E8F0; border-radius:8px;">
                    @forelse($rollingAttendance as $a)
                    @php
                        $m = $a->member;
                        $aDate = $a->attendance_date instanceof \Carbon\Carbon
                            ? $a->attendance_date
                            : \Carbon\Carbon::parse($a->attendance_date);
                    @endphp
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 14px; border-bottom:1px solid #F1F5F9; font-size:12.5px; font-family:'DM Sans',sans-serif;">
                        <span style="color:#0F172A; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $m ? $m->first_name.' '.$m->last_name : 'Unknown' }}
                        </span>
                        <span style="color:#64748B; font-size:11px; white-space:nowrap; flex-shrink:0; margin-left:10px;">
                            {{ $aDate->format('d M') }}
                        </span>
                    </div>
                    @empty
                    <p style="padding:20px; text-align:center; font-size:12.5px; color:#64748B; font-family:'DM Sans',sans-serif; margin:0;">
                        No check-ins in this window.
                    </p>
                    @endforelse
                </div>
            </div>

            {{-- Absent column --}}
            <div>
                <p style="margin:0 0 10px; font-size:12px; font-weight:600; color:#991B1B; font-family:'DM Sans',sans-serif;">
                    ✗ Absent ({{ $absentMembers->count() }})
                </p>
                <div style="max-height:320px; overflow-y:auto; border:1px solid #E2E8F0; border-radius:8px;">
                    @forelse($absentMembers as $m)
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:9px 14px; border-bottom:1px solid #F1F5F9; font-size:12.5px; font-family:'DM Sans',sans-serif;">
                        <span style="color:#0F172A; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $m->first_name }} {{ $m->last_name }}
                        </span>
                        <span style="color:#94A3B8; font-size:11px; white-space:nowrap; flex-shrink:0; margin-left:10px;">
                            {{ $m->phone ?? 'No phone' }}
                        </span>
                    </div>
                    @empty
                    <p style="padding:20px; text-align:center; font-size:12.5px; color:#64748B; font-family:'DM Sans',sans-serif; margin:0;">
                        Everyone has checked in recently 🎉
                    </p>
                    @endforelse
                </div>
            </div>
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
                <div style="display:flex; gap:8px; align-items:center; flex-shrink:0;">
                    <span style="display:inline-block; padding:2px 12px; border-radius:20px; font-size:11px; background:#DBEAFE; color:#1E40AF; font-family:'DM Sans',sans-serif; font-weight:500; white-space:nowrap;">
                        {{ $todayAttendance->count() }} present
                    </span>
                    @if($childrenToday > 0)
                    <span style="display:inline-block; padding:2px 12px; border-radius:20px; font-size:11px; background:#F3E8FF; color:#6B21A8; font-family:'DM Sans',sans-serif; font-weight:500; white-space:nowrap;">
                        {{ $childrenToday }} {{ Str::plural('child', $childrenToday) }}
                    </span>
                    @endif
                </div>
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
                        <p style="margin:0; font-size:11px; color:#64748B; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $a->phone ?? ($m ? $m->phone : 'N/A') }}</p>
                    </div>
                    @if($ch)
                    <span style="display:inline-block; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:500; white-space:nowrap; background:{{ $badgeStyle['bg'] }}; color:{{ $badgeStyle['text'] }}; max-width:80px; overflow:hidden; text-overflow:ellipsis;">
                        {{ $ch }}
                    </span>
                    @endif
                    @if(($a->children_count ?? 0) > 0)
                    <span title="Children checked in with this member" style="display:inline-flex; align-items:center; gap:3px; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:600; white-space:nowrap; background:#F3E8FF; color:#6B21A8; flex-shrink:0;">
                        🧒 {{ $a->children_count }}
                    </span>
                    @endif
                    <p style="margin:0; font-size:11px; color:#3B82F6; white-space:nowrap; flex-shrink:0;">
                        {{ $a->submitted_at ? $a->submitted_at->format('h:i A') : '—' }}
                    </p>
                </div>
                @empty
                <div style="padding:40px; text-align:center; font-size:14px; color:#64748B; font-family:'DM Sans',sans-serif;">
                    No records found for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}.
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
                    {{ $first->member ? $first->member->first_name : 'Guest' }}
                </p>
                <p style="margin:2px 0 0; font-size:12px; color:#3B82F6; font-family:'DM Sans',sans-serif;">{{ $first->submitted_at?->format('h:i A') }}</p>
                @else
                <p style="margin:0; font-size:13px; color:#64748B; font-family:'DM Sans',sans-serif;">No activity yet</p>
                @endif
            </div>

            {{-- Absent count --}}
            @php $absentCount = $totalMembers - $todayAttendance->count(); @endphp
            <div style="background:#FEF2F2; border:1.5px solid #FECACA; border-radius:12px; padding:18px 20px;">
                <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#991B1B; font-family:'DM Sans',sans-serif;">Absent Today</p>
                <p class="font-head" style="margin:0; font-size:36px; font-weight:700; color:#991B1B;">{{ max(0,$absentCount) }}</p>
                <p style="margin:2px 0 0; font-size:12px; color:#EF4444; font-family:'DM Sans',sans-serif;">from {{ $totalMembers }} members</p>
            </div>

            {{-- Top Church Today --}}
            @php
                $topChurch      = $churchStats->keys()->first() ?? '—';
                $topChurchCount = $churchStats->first() ?? 0;
                $topChurchPct   = $totalToday > 0 ? round(($topChurchCount / $totalToday) * 100) : 0;
            @endphp
            <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:12px; padding:18px 20px;">
                <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">Leading Church Today</p>
                <p class="font-head" style="margin:0; font-size:22px; color:#0F172A; font-weight:600; line-height:1.2; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    {{ $topChurch }}
                </p>
                <p style="margin:4px 0 0; font-size:12px; color:#3B82F6; font-family:'DM Sans',sans-serif;">
                    {{ $topChurchCount }} present · {{ $topChurchPct }}% share
                </p>
            </div>

            {{-- Children Today (moved into insights for small screens) --}}
            <div style="background:#FAF5FF; border:1.5px solid #E9D5FF; border-radius:12px; padding:18px 20px;">
                <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#6B21A8; font-family:'DM Sans',sans-serif;">Children Checked In</p>
                <p class="font-head" style="margin:0; font-size:36px; font-weight:700; color:#6B21A8;">{{ $childrenToday }}</p>
                <p style="margin:2px 0 0; font-size:12px; color:#A855F7; font-family:'DM Sans',sans-serif;">
                    across {{ $todayAttendance->where('children_count', '>', 0)->count() }} {{ Str::plural('family', $todayAttendance->where('children_count', '>', 0)->count()) }}
                </p>
            </div>

            {{-- Unique this week (June Support) --}}
            @php
                $weekStart   = \Carbon\Carbon::parse($date)->startOfWeek(\Carbon\Carbon::SUNDAY);
                $weekEnd     = $weekStart->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                $uniqueWeekCount = \App\Models\Attendance::whereDate('attendance_date', '>=', $weekStart->toDateString())
                    ->whereDate('attendance_date', '<=', $weekEnd->toDateString())
                    ->distinct('phone') // Using phone as unique identifier if email is NULL
                    ->count('phone');
            @endphp
            <div style="background:#F0FDF4; border:1.5px solid #86EFAC; border-radius:12px; padding:18px 20px;">
                <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#166534; font-family:'DM Sans',sans-serif;">Unique This Week</p>
                <p class="font-head" style="margin:0; font-size:36px; font-weight:700; color:#166534;">{{ $uniqueWeekCount }}</p>
                <p style="margin:2px 0 0; font-size:12px; color:#4ADE80; font-family:'DM Sans',sans-serif;">distinct participants</p>
            </div>
        </div>
    </div>
</div>

{{-- Send Absent SMS Modal --}}
<div id="sms-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl p-6 md:p-8 w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl md:text-2xl text-slate-800 font-semibold">Send SMS to Absentees</h2>
            <button type="button" onclick="document.getElementById('sms-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold p-1 text-lg">&times;</button>
        </div>
        <p class="text-sm text-slate-500 mb-5">
            This will message <strong>{{ $absentMembers->count() }}</strong> member(s) who haven't checked in over the last {{ $windowDays }} days ({{ \Carbon\Carbon::parse($windowStart)->format('d M') }} – {{ \Carbon\Carbon::parse($date)->format('d M Y') }}).
        </p>
        <form method="POST" action="{{ route('admin.dashboard.sms.absent') }}" id="sms-form">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <label class="block text-xs font-semibold uppercase tracking-wider text-blue-700 mb-2">Message</label>
            <textarea name="message" rows="4" maxlength="459" required
                class="w-full border border-blue-200 rounded-lg px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-y mb-1"
            >We missed you at Sunday Service! Hope to see you soon. God bless you.</textarea>
            <p class="text-xs text-slate-400 mb-5">Keep it under 459 characters (3 SMS segments). Sender ID must be approved in your BulkSMSNigeria dashboard.</p>
            <div class="flex justify-end gap-2.5">
                <button type="button" onclick="document.getElementById('sms-modal').classList.add('hidden')"
                    class="px-4 py-2.5 border border-blue-200 rounded-lg bg-white text-blue-700 text-sm font-medium hover:bg-blue-50/50 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="sms-submit-btn"
                    class="px-5 py-2.5 border-none rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors shadow-md shadow-blue-500/20">
                    Send Now
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Send Present SMS Modal --}}
<div id="sms-present-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl p-6 md:p-8 w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl md:text-2xl text-slate-800 font-semibold">Send SMS to Present Members</h2>
            <button type="button" onclick="document.getElementById('sms-present-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold p-1 text-lg">&times;</button>
        </div>
        <p class="text-sm text-slate-500 mb-5">
            This will message <strong>{{ $rollingAttendance->count() }}</strong> member(s) who checked in over the last {{ $windowDays }} days ({{ \Carbon\Carbon::parse($windowStart)->format('d M') }} – {{ \Carbon\Carbon::parse($date)->format('d M Y') }}).
        </p>
        <form method="POST" action="{{ route('admin.dashboard.sms.present') }}" id="sms-present-form">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <label class="block text-xs font-semibold uppercase tracking-wider text-green-700 mb-2">Message</label>
            <textarea name="message" rows="4" maxlength="459" required
                class="w-full border border-green-200 rounded-lg px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500 resize-y mb-1"
            >Thank you for joining us at Sunday Service! We're grateful you were here. God bless you.</textarea>
            <p class="text-xs text-slate-400 mb-5">Keep it under 459 characters (3 SMS segments). Sender ID must be approved in your BulkSMSNigeria dashboard.</p>
            <div class="flex justify-end gap-2.5">
                <button type="button" onclick="document.getElementById('sms-present-modal').classList.add('hidden')"
                    class="px-4 py-2.5 border border-green-200 rounded-lg bg-white text-green-700 text-sm font-medium hover:bg-green-50/50 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="sms-present-submit-btn"
                    class="px-5 py-2.5 border-none rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors shadow-md shadow-green-500/20">
                    Send Now
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('sms-form').addEventListener('submit', function () {
    const btn = document.getElementById('sms-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Sending…';
});

document.getElementById('sms-present-form').addEventListener('submit', function () {
    const btn = document.getElementById('sms-present-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Sending…';
});
</script>
@endpush
@endsection
