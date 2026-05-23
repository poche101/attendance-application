@extends('layouts.admin')
@section('title', 'Top Rankings')

@section('main')

{{-- Header --}}
<div class="mb-6">
    <p class="text-xs font-body tracking-widest uppercase text-slate-400 mb-1">Leaderboards</p>
    <h2 class="font-head text-2xl md:text-3xl text-slate-900 font-bold">Top 10 Rankings</h2>
</div>

{{-- Date range filter --}}
<form method="GET" action="{{ route('admin.rankings') }}" class="flex flex-col sm:flex-row sm:items-end gap-4 mb-8 p-4 bg-white border border-slate-200 rounded-2xl w-full lg:w-fit shadow-sm">
    <div class="w-full sm:w-auto">
        <label class="block text-xs font-body font-medium uppercase tracking-wider text-slate-400 mb-1.5">From</label>
        <input type="date" name="from" value="{{ $from }}" onchange="this.form.submit()"
            class="form-input w-full sm:w-[160px] font-body text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
    </div>
    <div class="w-full sm:w-auto">
        <label class="block text-xs font-body font-medium uppercase tracking-wider text-slate-400 mb-1.5">To</label>
        <input type="date" name="to" value="{{ $to }}" onchange="this.form.submit()"
            class="form-input w-full sm:w-[160px] font-body text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
    </div>
    <div class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2.5 bg-blue-50 rounded-xl border border-blue-100 w-full sm:w-auto self-stretch sm:self-auto h-[42px]">
        <span class="text-xs font-body text-blue-600 font-medium whitespace-nowrap">
            {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
        </span>
    </div>
</form>

{{-- Three ranking cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach([
        ['title'=>'Top Cells',    'data'=>$cells,    'icon'=>'🏆', 'accent'=>'#1E40AF', 'soft'=>'#DBEAFE', 'bar'=>'bg-blue-500'],
        ['title'=>'Top Groups',   'data'=>$groups,   'icon'=>'🎯', 'accent'=>'#065f46', 'soft'=>'#d1fae5', 'bar'=>'bg-emerald-500'],
        ['title'=>'Top Churches', 'data'=>$churches, 'icon'=>'⛪', 'accent'=>'#7c3aed', 'soft'=>'#ede9fe', 'bar'=>'bg-violet-500'],
    ] as $card)
    @php $maxVal = $card['data']->first()['count'] ?? 1; @endphp

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm flex flex-col justify-between">

        <div>
            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3"
                 style="background: linear-gradient(135deg, {{ $card['soft'] }}55 0%, #fff 100%);">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0"
                     style="background:{{ $card['soft'] }};">
                    {{ $card['icon'] }}
                </div>
                <div class="min-w-0">
                    <h3 class="font-head text-lg text-slate-900 font-bold leading-tight truncate">{{ $card['title'] }}</h3>
                    <p class="text-xs font-body text-slate-400 mt-0.5">{{ $card['data']->count() }} entries</p>
                </div>
            </div>

            {{-- Rows --}}
            <div class="px-6 py-4">
                @if($card['data']->isEmpty())
                    <div class="text-center py-12">
                        <p class="text-slate-400 font-body text-sm">No data in selected range.</p>
                    </div>
                @endif

                @foreach($card['data'] as $i => $item)
                @php
                    $pct    = round(($item['count'] / $maxVal) * 100);
                    $medals = ['🥇','🥈','🥉'];
                @endphp
                <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">

                    {{-- Rank --}}
                    <div class="w-7 text-center flex-shrink-0">
                        @if($i < 3)
                            <span class="text-xl inline-block transform scale-110">{{ $medals[$i] }}</span>
                        @else
                            <span class="font-head text-xs font-bold text-slate-400">#{{ $i + 1 }}</span>
                        @endif
                    </div>

                    {{-- Bar + label --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-1.5 gap-2">
                            <span class="font-body text-sm font-medium text-slate-800 truncate" title="{{ $item['name'] }}">
                                {{ $item['name'] }}
                            </span>
                            <span class="font-head text-sm font-bold flex-shrink-0 ml-auto whitespace-nowrap"
                                  style="color:{{ $card['accent'] }};">
                                {{ $item['count'] }}
                                <span class="font-body font-normal text-slate-400 text-xs ml-0.5">pts</span>
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="{{ $card['bar'] }} h-1.5 rounded-full transition-all duration-500 shadow-sm"
                                 style="width:{{ $pct }}%;"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer total --}}
        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50 mt-auto">
            <p class="text-xs font-body text-slate-500 m-0">
                Total: <span class="font-semibold text-slate-700 bg-white px-2 py-0.5 rounded border border-slate-100 shadow-sm">{{ $card['data']->sum('count') }}</span> check-ins
            </p>
        </div>
    </div>
    @endforeach
</div>

@endsection
