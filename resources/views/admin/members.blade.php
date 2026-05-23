@extends('layouts.admin')
@section('title', 'Members')

@section('main')
@php
    $bgs   = ['#dbeafe','#e0f2fe','#d1fae5','#f3e8ff','#fef3c7','#fee2e2','#fce7f3','#ecfdf5'];
    $texts = ['#1e40af','#0369a1','#166534','#6b21a8','#92400e','#991b1b','#9d174d','#0f766e'];
@endphp

{{-- Header --}}
<div style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:20px;">
    <div>
        <span style="font-size:12px; letter-spacing:0.12em; text-transform:uppercase; color:#2563eb; font-family:var(--font-body);">Directory</span>
        <h2 style="font-size:32px; margin:4px 0 0; color:#1e2937; font-weight:600;">Member Management</h2>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('admin.export.csv') }}" class="btn-outline" style="font-size:12px; padding:8px 16px;">↓ Export CSV</a>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')"
                class="btn-primary"
                style="background:#3b82f6; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:500;">
            + Add New Member
        </button>
    </div>
</div>

{{-- Summary cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px;">
    @php
        $totalActive  = $members->total();
        $presentCount = count(array_filter($todayEmails ?? []));
        $churchCount  = \App\Models\Member::where('is_active', true)
                            ->distinct('church')
                            ->count('church');
        $topChurch    = \App\Models\Member::where('is_active', true)
                            ->whereNotNull('church')
                            ->selectRaw('church, COUNT(*) as total')
                            ->groupBy('church')
                            ->orderByDesc('total')
                            ->value('church') ?? '—';
    @endphp
    @foreach([
        ['Total Members', $totalActive,  '#1e40af', '#dbeafe'],
        ['Present Today', $presentCount, '#166534', '#d1fae5'],
        ['Churches',      $churchCount,  '#6b21a8', '#f3e8ff'],
        ['Top Church',    $topChurch,    '#92400e', '#fef3c7'],
    ] as [$lbl, $val, $tc, $bg])
    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px;">
        <p style="margin:0 0 6px; font-size:12px; color:#64748b; font-weight:500;">{{ $lbl }}</p>
        <p style="margin:0; font-size:{{ is_numeric($val) ? '32' : '20' }}px; font-weight:700; color:#1e2937; line-height:1.2;">{{ $val }}</p>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.members.index') }}" style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
    <input type="text" name="search" placeholder="🔍 Search name or email…" value="{{ request('search') }}"
        style="flex:2; min-width:200px; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; font-size:14px; background:white;">

    <input type="text" name="cell" placeholder="Filter by cell…" value="{{ request('cell') }}"
        style="flex:1; min-width:140px; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; font-size:14px; background:white;">

    <select name="church" onchange="this.form.submit()"
        style="flex:1; min-width:160px; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; font-size:14px; background:white;">
        <option value="">All Churches</option>
        @foreach($churches ?? [] as $c)
        <option value="{{ $c }}" {{ request('church') === $c ? 'selected' : '' }}>{{ $c }}</option>
        @endforeach
    </select>

    <input type="date" name="date_from" value="{{ request('date_from') }}"
        style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; font-size:14px; background:white;">

    <input type="date" name="date_to" value="{{ request('date_to') }}"
        style="border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; font-size:14px; background:white;">

    <button type="submit" class="btn-primary" style="background:#2563eb; color:white; padding:12px 24px;">Search</button>

    @if(request()->hasAny(['search','cell','church','date_from','date_to']))
        <a href="{{ route('admin.members.index') }}" class="btn-outline" style="padding:12px 20px;">Clear Filters</a>
    @endif
</form>

{{-- Table --}}
<div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                    @php
                        $cols = [
                            ['',''],
                            ['title','Title'],
                            ['first_name','Name'],
                            ['email','Email'],
                            ['cell','Cell'],
                            ['church','Church'],
                            ['','Attendance'],
                            ['','Status'],
                            ['','Actions']
                        ];
                    @endphp
                    @foreach($cols as [$key,$label])
                    <th style="padding:14px 16px; text-align:left; font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">
                        @if($key)
                        <a href="{{ request()->fullUrlWithQuery(['sort'=>$key,'dir'=>($sortCol===$key&&$sortDir==='asc')?'desc':'asc']) }}"
                           style="color:#64748b; text-decoration:none;">
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
                    $idx      = $m->id % 8;
                    $present  = in_array(strtolower(trim($m->email)), $todayEmails ?? []);
                    $attCount = $memberAttCounts[$m->id] ?? 0;
                @endphp
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 16px;">
                        <div style="width:38px; height:38px; border-radius:50%; background:{{ $bgs[$idx] }}; color:{{ $texts[$idx] }};
                                    display:flex; align-items:center; justify-content:center; font-weight:600; font-size:13px;">
                            {{ $m->initials ?? strtoupper(substr($m->first_name ?? '', 0, 1)) }}
                        </div>
                    </td>
                    <td style="padding:14px 16px; font-weight:500; color:#334155;">{{ $m->title ?? '—' }}</td>
                    <td style="padding:14px 16px;">
                        <p style="margin:0; font-weight:600; color:#1e2937;">{{ $m->first_name }} {{ $m->last_name }}</p>
                    </td>
                    <td style="padding:14px 16px;">
                        <div style="font-size:13.5px; color:#334155;">{{ $m->email }}</div>
                        <div style="font-size:12px; color:#64748b;">{{ $m->phone }}</div>
                    </td>
                    <td style="padding:14px 16px;">
                        <span style="padding:4px 12px; border-radius:9999px; font-size:12.5px; background:#f1f5f9; color:#475569;">
                            {{ $m->cell ?? '—' }}
                        </span>
                    </td>
                    <td style="padding:14px 16px; color:#475569;">{{ $m->church ?? '—' }}</td>
                    <td style="padding:14px 16px; font-weight:600; color:#2563eb;">{{ $attCount }}×</td>
                    <td style="padding:14px 16px;">
                        <span style="padding:4px 12px; border-radius:9999px; font-size:12.5px; font-weight:500;
                            background:{{ $present ? '#ecfdf5' : '#fef2f2' }};
                            color:{{ $present ? '#166534' : '#991b1b' }};">
                            {{ $present ? '✓ Present' : '✗ Absent' }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;">
                        <div style="display:flex; gap:6px;">
                            <button onclick="openEdit({{ $m->toJson() }})"
                                    class="btn-outline btn-sm"
                                    style="padding:6px 12px; border-radius:6px; font-size:13px;">
                                ✎ Edit
                            </button>
                            <form method="POST" action="{{ route('admin.members.destroy', $m) }}" style="display:inline;" id="delete-form-{{ $m->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        onclick="confirmDelete({{ $m->id }}, '{{ $m->first_name }} {{ $m->last_name }}')"
                                        class="btn-outline btn-sm"
                                        style="padding:6px 12px; border-radius:6px; font-size:13px; color:#ef4444; border-color:#fecaca;">
                                    🗑 Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="padding:80px; text-align:center; color:#94a3b8;">No members found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="padding:16px 24px; display:flex; justify-content:space-between; align-items:center; border-top:1px solid #e2e8f0;">
        <span style="color:#64748b;">{{ $members->total() }} members • Page {{ $members->currentPage() }} of {{ $members->lastPage() }}</span>
        <div style="display:flex; gap:8px;">
            @if($members->onFirstPage())
                <span class="btn-outline btn-sm" style="opacity:0.4;">← Prev</span>
            @else
                <a href="{{ $members->previousPageUrl() }}" class="btn-outline btn-sm">← Prev</a>
            @endif
            @if($members->hasMorePages())
                <a href="{{ $members->nextPageUrl() }}" class="btn-outline btn-sm">Next →</a>
            @else
                <span class="btn-outline btn-sm" style="opacity:0.4;">Next →</span>
            @endif
        </div>
    </div>
</div>

{{-- Add Member Modal --}}
<div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div style="background:#fff; border-radius:16px; padding:32px; width:560px; max-width:95vw; max-height:90vh; overflow-y:auto;" onclick="event.stopPropagation()">
        <h2 style="margin:0 0 24px; font-size:26px; color:#1e2937; font-weight:600;">Add New Member</h2>
        <form method="POST" action="{{ route('admin.members.store') }}">
            @csrf
            @include('admin._member_form')
        </form>
    </div>
</div>

{{-- Edit Member Modal --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div style="background:#fff; border-radius:16px; padding:32px; width:560px; max-width:95vw; max-height:90vh; overflow-y:auto;" onclick="event.stopPropagation()">
        <h2 style="margin:0 0 24px; font-size:26px; color:#1e2937; font-weight:600;">Edit Member</h2>
        <form method="POST" id="edit-form" action="">
            @csrf @method('PUT')
            @include('admin._member_form', ['editing' => true])
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="delete-confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div style="background:#fff; border-radius:16px; padding:32px; width:400px; max-width:95vw;" onclick="event.stopPropagation()">
        <h3 style="margin:0 0 16px; color:#1e2937; font-size:20px;">Delete Member?</h3>
        <p id="delete-member-name" style="margin:0 0 24px; color:#475569; font-size:15px;"></p>
        <div style="display:flex; gap:12px; justify-content:flex-end;">
            <button onclick="closeDeleteModal()"
                style="padding:10px 22px; border:1.5px solid #BFDBFE; border-radius:8px; background:#fff; color:#1E40AF; font-family:'DM Sans',sans-serif; font-size:14px; font-weight:500; cursor:pointer;"
                onmouseover="this.style.background='#EFF6FF'" onmouseout="this.style.background='#fff'">
                Cancel
            </button>
            <button onclick="executeDelete()"
                style="padding:10px 24px; border:none; border-radius:8px; background:#ef4444; color:white; font-family:'DM Sans',sans-serif; font-size:14px; font-weight:600; cursor:pointer; box-shadow:0 4px 14px rgba(239,68,68,.3);"
                onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                Yes, Delete
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentDeleteUrl = '';

function openEdit(member) {
    const form = document.getElementById('edit-form');
    form.action = `/admin/members/${member.id}`;

    const fields = ['title','first_name','last_name','email','phone','cell','church','birthday'];
    fields.forEach(k => {
        const el = form.querySelector(`[name="${k}"]`);
        if (el) el.value = member[k] ?? '';
    });

    document.getElementById('modal-edit').classList.remove('hidden');
}

function confirmDelete(id, name) {
    currentDeleteUrl = `/admin/members/${id}`;
    document.getElementById('delete-member-name').textContent = `Are you sure you want to delete ${name}?`;
    document.getElementById('delete-confirm-modal').classList.remove('hidden');
}

function executeDelete() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = currentDeleteUrl;
    form.style.display = 'none';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    form.appendChild(method);

    document.body.appendChild(form);
    form.submit();
}

function closeDeleteModal() {
    document.getElementById('delete-confirm-modal').classList.add('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}
</script>
@endpush
