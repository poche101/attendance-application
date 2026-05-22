@extends('layouts.admin')
@section('title', 'Top Rankings')

@section('main')
<div style="margin-bottom:24px;">
    <span style="font-size:12px; letter-spacing:0.12em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">Leaderboards</span>
    <h2 class="cg" style="font-size:32px; margin:4px 0 16px; color:#1E1208; font-weight:500;">Top 10 Rankings</h2>
    <form method="GET" action="{{ route('admin.rankings') }}" style="display:flex; gap:12px;">
        <div>
            <label style="font-size:11px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:5px; display:block; font-family:'Jost',sans-serif;">From</label>
            <input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()"
                style="width:160px; border:1px solid #FAD9B5; border-radius:8px; padding:8px 12px; font-size:13px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
        </div>
        <div>
            <label style="font-size:11px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:5px; display:block; font-family:'Jost',sans-serif;">To</label>
            <input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()"
                style="width:160px; border:1px solid #FAD9B5; border-radius:8px; padding:8px 12px; font-size:13px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
        </div>
    </form>
</div>

<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px;">
    @foreach([['Top Cells', $cells, '🏆'],['Top Groups',$groups,'🎯'],['Top Churches',$churches,'⛪']] as [$title,$data,$icon])
    @php $maxVal = $data->first()['count'] ?? 1; @endphp
    <div style="background:#fff; border:1px solid #FAD9B5; border-radius:12px; overflow:hidden;">
        <div style="padding:18px 20px; border-bottom:1px solid #FAD9B5; display:flex; align-items:center; gap:10px;">
            <span style="font-size:20px;">{{ $icon }}</span>
            <h3 class="cg" style="margin:0; font-size:20px; color:#1E1208; font-weight:500;">{{ $title }}</h3>
        </div>
        <div style="padding:14px 20px;">
            @if($data->isEmpty())
            <p style="font-size:13px; color:#F0A055; font-family:'Jost',sans-serif;">No data in selected range.</p>
            @endif
            @foreach($data as $i => $item)
            @php $pct = round(($item['count']/$maxVal)*100); @endphp
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <span style="font-size:16px; width:26px; text-align:center; flex-shrink:0;">
                    {{ ['🥇','🥈','🥉'][$i] ?? '#'.($i+1) }}
                </span>
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                        <span style="font-size:13px; color:#1E1208; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:72%; font-family:'Jost',sans-serif;">
                            {{ $item['name'] }}
                        </span>
                        <span style="font-size:12px; color:#C45E08; font-weight:600; font-family:'Jost',sans-serif; flex-shrink:0; margin-left:6px;">
                            {{ $item['count'] }}
                        </span>
                    </div>
                    <div style="background:#FEE9CF; border-radius:4px; height:7px;">
                        <div style="background:linear-gradient(90deg,#F0A055,#C45E08); border-radius:4px; height:7px; width:{{ $pct }}%;"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
