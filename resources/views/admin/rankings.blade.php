@extends('layouts.admin')
@section('title', 'Top Rankings')

@section('main')

{{-- Header --}}
<div class="mb-6">
    <p class="text-xs font-body tracking-widest uppercase text-slate-400 mb-1">Leaderboards</p>
    <h2 class="font-head text-3xl text-slate-900">Top 10 Rankings</h2>
</div>

{{-- Date range filter --}}
<form method="GET" action="{{ route('admin.rankings') }}" class="flex items-end gap-4 mb-8 p-4 bg-white border border-slate-200 rounded-2xl w-fit">
    <div>
        <label class="block text-xs font-body font-medium uppercase tracking-wider text-slate-400 mb-1.5">From</label>
        <input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()"
            class="form-input font-body text-sm text-slate-800 bg-slate-50 focus:outline-none"
            style="width:160px;">
    </div>
    <div>
        <label class="block text-xs font-body font-medium uppercase tracking-wider text-slate-400 mb-1.5">To</label>
        <input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()"
            class="form-input font-body text-sm text-slate-800 bg-slate-50 focus:outline-none"
            style="width:160px;">
    </div>
    <div class="flex items-center gap-2 px-4 py-2.5 bg-blue-50 rounded-xl border border-blue-100">
        <span class="text-xs font-body text-blue-600 font-medium">
            {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        </span>
    </div>
</form>

{{-- Three ranking cards --}}
<div class="grid grid-cols-3 gap-6">
    @foreach([
        ['title'=>'Top Cells',    'data'=>$cells,    'icon'=>'🏆', 'accent'=>'#1E40AF', 'soft'=>'#DBEAFE', 'bar'=>'bg-blue-500'],
        ['title'=>'Top Groups',   'data'=>$groups,   'icon'=>'🎯', 'accent'=>'#065f46', 'soft'=>'#d1fae5', 'bar'=>'bg-emerald-500'],
        ['title'=>'Top Churches', 'data'=>$churches, 'icon'=>'⛪', 'accent'=>'#7c3aed', 'soft'=>'#ede9fe', 'bar'=>'bg-violet-500'],
    ] as $card)
    @php $maxVal = $card['data']->first()['count'] ?? 1; @endphp

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">

        {{-- Card header --}}
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3"
             style="background: linear-gradient(135deg, {{ $card['soft'] }}55 0%, #fff 100%);">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                 style="background:{{ $card['soft'] }};">
                {{ $card['icon'] }}
            </div>
            <div>
                <h3 class="font-head text-lg text-slate-900 leading-tight">{{ $card['title'] }}</h3>
                <p class="text-xs font-body text-slate-400 mt-0.5">{{ $card['data']->count() }} entries</p>
            </div>
        </div>

        {{-- Rows --}}
        <div class="px-6 py-4">
            @if($card['data']->isEmpty())
                <div class="text-center py-8">
                    <p class="text-slate-400 font-body text-sm">No data in selected range.</p>
                </div>
            @endif

            @foreach($card['data'] as $i => $item)
            @php
                $pct    = round(($item['count'] / $maxVal) * 100);
                $medals = ['🥇','🥈','🥉'];
            @endphp
            <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-slate-50' : '' }}">

                {{-- Rank --}}
                <div class="w-7 text-center flex-shrink-0">
                    @if($i < 3)
                        <span class="text-lg">{{ $medals[$i] }}</span>
                    @else
                        <span class="font-head text-sm text-slate-400">#{{ $i + 1 }}</span>
                    @endif
                </div>

                {{-- Bar + label --}}
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-baseline mb-1.5">
                        <span class="font-body text-sm font-medium text-slate-800 truncate max-w-[65%]">
                            {{ $item['name'] }}
                        </span>
                        <span class="font-head text-sm font-bold flex-shrink-0 ml-2"
                              style="color:{{ $card['accent'] }};">
                            {{ $item['count'] }}
                            <span class="font-body font-normal text-slate-400 text-xs">pts</span>
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="{{ $card['bar'] }} h-1.5 rounded-full transition-all duration-500"
                             style="width:{{ $pct }}%;"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Footer total --}}
        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50">
            <p class="text-xs font-body text-slate-400">
                Total: <span class="font-semibold text-slate-600">{{ $card['data']->sum('count') }}</span> check-ins
            </p>
        </div>
    </div>
    @endforeach
</div>

@endsection
