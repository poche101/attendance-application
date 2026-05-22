@extends('layouts.admin')
@section('title', 'Members')

@section('main')
@php
    $groupColors = ['Women'=>['bg'=>'#fff0e8','text'=>'#9d174d'],'Men'=>['bg'=>'#eff6ff','text'=>'#1e40af'],'Youth'=>['bg'=>'#f0fdf4','text'=>'#166534'],'Choir'=>['bg'=>'#fff7ed','text'=>'#9a3412']];
    $bgs   = ['#dbeafe','#fce7f3','#d1fae5','#fef3c7','#ede9fe','#fee2e2','#e0f2fe','#fef9c3'];
    $texts = ['#1e40af','#9d174d','#065f46','#92400e','#5b21b6','#991b1b','#0369a1','#78350f'];
@endphp

{{-- Header --}}
<div style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:20px;">
    <div>
        <span style="font-size:12px; letter-spacing:0.12em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">Directory</span>
        <h2 class="cg" style="font-size:32px; margin:4px 0 0; color:#1E1208; font-weight:500;">Member Management</h2>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('admin.export.csv') }}" class="btn-outline" style="font-size:12px; padding:8px 16px;">↓ Export CSV</a>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="btn-primary">+ Add Member</button>
    </div>
</div>

{{-- Summary cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px;">
    @php
        $totalActive   = $members->total();
        $presentCount  = count(array_filter($todayEmails));
        $groups        = \App\Models\Member::where('is_active',true)->distinct()->pluck('group');
    @endphp
    @foreach([
        ['Total Members',  \App\Models\Member::where('is_active',true)->count(), '#1e40af','#dbeafe'],
        ['Present Today',  count($todayEmails),                              '#065f46','#d1fae5'],
        ['Groups',         \App\Models\Member::where('is_active',true)->distinct('group')->count('group'), '#92400e','#fef3c7'],
        ['Churches',       \App\Models\Member::where('is_active',true)->distinct('church')->count('church'),'#5b21b6','#ede9fe'],
    ] as [$lbl,$val,$tc,$bg])
    <div style="background:#fff; border:1px solid #FAD9B5; border-radius:10px; padding:16px 18px;">
        <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.09em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">{{ $lbl }}</p>
        <p class="cg" style="margin:0; font-size:28px; font-weight:600; color:#1E1208;">{{ $val }}</p>
        <div style="margin-top:8px; height:2px; border-radius:2px; background:{{ $bg }};"></div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.members') }}" style="display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap;">
    <input type="text" name="search" placeholder="🔍  Search name or email…" value="{{ request('search') }}"
        style="flex:2; min-width:180px; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:13px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    <input type="text" name="group" placeholder="Filter by group…" value="{{ request('group') }}"
        style="flex:1; min-width:130px; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:13px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    <select name="church" onchange="this.form.submit()"
        style="flex:1; min-width:150px; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:13px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
        <option value="">All Churches</option>
        @foreach($churches as $c)
        <option value="{{ $c }}" {{ request('church') === $c ? 'selected' : '' }}>{{ $c }}</option>
        @endforeach
    </select>
    <input type="date" name="date_from" value="{{ request('date_from') }}" title="Attended from"
        style="border:1px solid #FAD9B5; border-radius:8px; padding:10px 12px; font-size:13px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    <input type="date" name="date_to" value="{{ request('date_to') }}" title="Attended to"
        style="border:1px solid #FAD9B5; border-radius:8px; padding:10px 12px; font-size:13px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    <select name="status" onchange="this.form.submit()"
        style="border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:13px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
        <option value="">All Status</option>
        <option value="present" {{ request('status')==='present' ? 'selected' : '' }}>Present Today</option>
        <option value="absent"  {{ request('status')==='absent'  ? 'selected' : '' }}>Absent Today</option>
    </select>
    <button type="submit" class="btn-primary" style="padding:10px 20px; font-size:13px;">Search</button>
    @if(request()->hasAny(['search','group','church','date_from','date_to','status']))
    <a href="{{ route('admin.members') }}" class="btn-outline" style="padding:10px 16px; font-size:13px;">✕ Clear</a>
    @endif
</form>

{{-- Table --}}
<div style="background:#fff; border:1px solid #FAD9B5; border-radius:12px; overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <colgroup>
                <col style="width:46px"><col style="width:17%"><col style="width:20%">
                <col style="width:11%"><col style="width:14%"><col style="width:10%">
                <col style="width:9%"><col style="width:9%"><col style="width:90px">
            </colgroup>
            <thead>
                <tr style="background:#FFF5E8; border-bottom:1px solid #FAD9B5;">
                    @php $cols = [['',''],['first_name','Name'],['email','Email'],['group','Group'],['church','Church'],['cell','Cell'],['','Attendance'],['','Status'],['','Actions']]; @endphp
                    @foreach($cols as [$key,$label])
                    <th style="padding:11px 13px; text-align:left; font-size:11px; letter-spacing:0.08em; text-transform:uppercase; color:#B86A1A; font-weight:500; font-family:'Jost',sans-serif;">
                        @if($key)
                        <a href="{{ request()->fullUrlWithQuery(['sort'=>$key,'dir'=>($sortCol===$key&&$sortDir==='asc')?'desc':'asc']) }}"
                           style="color:#B86A1A; text-decoration:none; display:flex; align-items:center; gap:3px;">
                            {{ $label }} @if($sortCol===$key) {{ $sortDir==='asc'?'↑':'↓' }} @endif
                        </a>
                        @else {{ $label }} @endif
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($members as $m)
                @php
                    $idx     = $m->id % 8;
                    $present = in_array(strtolower(trim($m->email)), $todayEmails);
                    $attCount = $memberAttCounts[$m->id] ?? 0;
                @endphp
                <tr style="border-bottom:1px solid #FDF0DC; opacity:{{ $m->is_active ? 1 : 0.5 }}; transition:background 0.15s;"
                    onmouseover="this.style.background='#FFF8EE'" onmouseout="this.style.background='transparent'">
                    <td style="padding:10px 13px;">
                        <div style="width:34px; height:34px; border-radius:50%; background:{{ $bgs[$idx] }}; color:{{ $texts[$idx] }}; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; font-family:'Jost',sans-serif;">
                            {{ $m->initials }}
                        </div>
                    </td>
                    <td style="padding:10px 13px;">
                        <p style="margin:0; font-size:13px; font-weight:500; color:#1E1208; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-family:'Jost',sans-serif;">{{ $m->first_name }} {{ $m->last_name }}</p>
                        <p style="margin:0; font-size:10px; color:#B86A1A; font-family:'Jost',sans-serif;">{{ $m->birthday?->format('d M Y') ?? '—' }}</p>
                    </td>
                    <td style="padding:10px 13px;">
                        <span style="font-size:12px; color:#7A3E08; overflow:hidden; text-overflow:ellipsis; display:block; white-space:nowrap; font-family:'Jost',sans-serif;">{{ $m->email }}</span>
                        <span style="font-size:10px; color:#B86A1A; font-family:'Jost',sans-serif;">{{ $m->phone }}</span>
                    </td>
                    <td style="padding:10px 13px;">
                        <span style="display:inline-block; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:500; font-family:'Jost',sans-serif;
                            background:{{ $groupColors[$m->group]['bg'] ?? '#FEE9CF' }}; color:{{ $groupColors[$m->group]['text'] ?? '#C45E08' }};">
                            {{ $m->group }}
                        </span>
                    </td>
                    <td style="padding:10px 13px;"><span style="font-size:12px; color:#7A3E08; overflow:hidden; text-overflow:ellipsis; display:block; white-space:nowrap; font-family:'Jost',sans-serif;">{{ $m->church ?? '—' }}</span></td>
                    <td style="padding:10px 13px;"><span style="font-size:12px; color:#7A3E08; font-family:'Jost',sans-serif;">{{ $m->cell ?? '—' }}</span></td>
                    <td style="padding:10px 13px;">
                        <span style="font-size:12px; font-weight:600; color:#C45E08; font-family:'Jost',sans-serif;">{{ $attCount }}×</span>
                    </td>
                    <td style="padding:10px 13px;">
                        <span style="display:inline-block; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:500; font-family:'Jost',sans-serif;
                            background:{{ $present ? '#d1fae5' : '#fee2e2' }}; color:{{ $present ? '#065f46' : '#991b1b' }};">
                            {{ $present ? '✓ Present' : '✗ Absent' }}
                        </span>
                    </td>
                    <td style="padding:10px 13px;">
                        <div style="display:flex; gap:5px;">
                            <button onclick="openEdit({{ $m->toJson() }})" class="btn-outline btn-sm" title="Edit">✎</button>
                            @if($m->is_active)
                            <form method="POST" action="{{ route('admin.members.deactivate', $m) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-outline btn-sm" style="color:#991b1b; border-color:#fecaca;" title="Deactivate" onclick="return confirm('Deactivate this member?')">✕</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="padding:40px; text-align:center; font-size:14px; color:#F0A055; font-family:'Jost',sans-serif;">No members found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 24px; display:flex; justify-content:space-between; align-items:center; border-top:1px solid #FAD9B5;">
        <span style="font-size:12px; color:#B86A1A; font-family:'Jost',sans-serif;">
            {{ $members->total() }} members · Page {{ $members->currentPage() }} of {{ $members->lastPage() }}
        </span>
        <div style="display:flex; gap:8px;">
            @if($members->onFirstPage())
                <span class="btn-outline btn-sm" style="opacity:0.4; cursor:not-allowed;">← Prev</span>
            @else
                <a href="{{ $members->previousPageUrl() }}" class="btn-outline btn-sm">← Prev</a>
            @endif
            @if($members->hasMorePages())
                <a href="{{ $members->nextPageUrl() }}" class="btn-outline btn-sm">Next →</a>
            @else
                <span class="btn-outline btn-sm" style="opacity:0.4; cursor:not-allowed;">Next →</span>
            @endif
        </div>
    </div>
</div>

{{-- Add Member Modal --}}
<div id="modal-add" class="hidden fixed inset-0 z-[100]" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.3); backdrop-filter:blur(2px); align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; padding:32px; width:520px; max-width:95vw; max-height:90vh; overflow-y:auto;" onclick="event.stopPropagation()">
        <h2 class="cg" style="margin:0 0 24px; font-size:26px; color:#1E1208; font-weight:500;">Add New Member</h2>
        <form method="POST" action="{{ route('admin.members.store') }}">
            @csrf
            @include('admin._member_form')
            <div style="display:flex; gap:12px; margin-top:28px; justify-content:flex-end;">
                <button type="button" onclick="closeModal('modal-add')" class="btn-outline">Cancel</button>
                <button type="submit" class="btn-primary">Save Member</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Member Modal --}}
<div id="modal-edit" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.3); backdrop-filter:blur(2px); align-items:center; justify-content:center; z-index:100;">
    <div style="background:#fff; border-radius:16px; padding:32px; width:520px; max-width:95vw; max-height:90vh; overflow-y:auto;" onclick="event.stopPropagation()">
        <h2 class="cg" style="margin:0 0 24px; font-size:26px; color:#1E1208; font-weight:500;">Edit Member</h2>
        <form method="POST" id="edit-form" action="">
            @csrf @method('PUT')
            @include('admin._member_form', ['editing' => true])
            <div style="display:flex; gap:12px; margin-top:28px; justify-content:flex-end;">
                <button type="button" onclick="closeModal('modal-edit')" class="btn-outline">Cancel</button>
                <button type="submit" class="btn-primary">Save Member</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(id) {
    const el = document.getElementById(id);
    el.style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
function openEdit(member) {
    const form = document.getElementById('edit-form');
    form.action = `/admin/members/${member.id}`;
    ['first_name','last_name','email','phone','group','church','cell','birthday'].forEach(k => {
        const el = form.querySelector(`[name="${k}"]`);
        if (el) el.value = member[k] ?? '';
    });
    openModal('modal-edit');
}
document.querySelector('[onclick*="modal-add"]').addEventListener('click', () => openModal('modal-add'));
</script>
@endpush