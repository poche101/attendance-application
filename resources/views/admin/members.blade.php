@extends('layouts.admin')
@section('title', 'Members')

@section('main')
@php
    $bgs   = ['#dbeafe','#e0f2fe','#d1fae5','#f3e8ff','#fef3c7','#fee2e2','#fce7f3','#ecfdf5'];
    $texts = ['#1e40af','#0369a1','#166534','#6b21a8','#92400e','#991b1b','#9d174d','#0f766e'];
@endphp

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
    <div>
        <span class="text-xs font-body uppercase tracking-widest text-blue-600">Directory</span>
        <h2 class="text-2xl md:text-3xl mt-1 text-slate-800 font-semibold tracking-tight">Member Management</h2>
    </div>
    <div class="flex flex-wrap gap-2.5">
        <a href="{{ route('admin.export.csv') }}" class="btn-outline text-xs px-4 py-2.5 rounded-lg flex items-center justify-center bg-white">
            ↓ Export CSV
        </a>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')"
                class="btn-primary bg-blue-500 hover:bg-blue-600 text-white text-sm px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm whitespace-nowrap">
            + Add New Member
        </button>
    </div>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm transition-all hover:border-slate-300">
        <p class="m-0 mb-1 text-xs text-slate-500 font-medium tracking-wide">{{ $lbl }}</p>
        <p class="m-0 text-2xl lg:text-3xl font-bold text-slate-800 line-clamp-1 break-all" style="{{ !is_numeric($val) ? 'font-size: 1.25rem;' : '' }}">{{ $val }}</p>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.members.index') }}" class="w-full flex flex-col md:flex-row gap-2.5 mb-6">
    <div class="w-full md:flex-[2] min-w-0">
        <input type="text" name="search" placeholder="🔍 Search name or email…" value="{{ request('search') }}"
            class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
    </div>

    <div class="w-full md:flex-[1] min-w-0">
        <input type="text" name="cell" placeholder="Filter by cell…" value="{{ request('cell') }}"
            class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
    </div>

    <div class="w-full md:flex-[1] min-w-0">
        <select name="church" onchange="this.form.submit()"
            class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
            <option value="">All Churches</option>
            @foreach($churches ?? [] as $c)
            <option value="{{ $c }}" {{ request('church') === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 gap-2 w-full md:w-auto">
        <input type="date" name="date_from" value="{{ request('date_from') }}"
            class="w-full border border-slate-200 rounded-lg px-3 py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">

        <input type="date" name="date_to" value="{{ request('date_to') }}"
            class="w-full border border-slate-200 rounded-lg px-3 py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
    </div>

    <div class="flex gap-2 w-full md:w-auto mt-1 md:mt-0">
        <button type="submit" class="btn-primary flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-6 py-3 text-sm shadow-sm transition-colors text-center">
            Search
        </button>

        @if(request()->hasAny(['search','cell','church','date_from','date_to']))
            <a href="{{ route('admin.members.index') }}" class="btn-outline flex-1 md:flex-none text-center bg-white px-5 py-3 text-sm rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                Clear
            </a>
        @endif
    </div>
</form>

{{-- Table Container --}}
<div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
    <div class="w-full overflow-x-auto scrollbar-thin">
        <table class="w-full border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
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
                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">
                        @if($key)
                        <a href="{{ request()->fullUrlWithQuery(['sort'=>$key,'dir'=>($sortCol===$key&&$sortDir==='asc')?'desc':'asc']) }}"
                           class="text-slate-500 hover:text-slate-700 inline-flex items-center gap-1 transition-colors">
                            {{ $label }} @if($sortCol===$key) <span>{{ $sortDir==='asc'?'↑':'↓' }}</span> @endif
                        </a>
                        @else {{ $label }} @endif
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($members as $m)
                @php
                    $idx      = $m->id % 8;
                    $present  = in_array(strtolower(trim($m->email)), $todayEmails ?? []);
                    $attCount = $memberAttCounts[$m->id] ?? 0;
                @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-4 py-3.5 whitespace-nowrap w-12">
                        <div class="w-9 h-9 rounded-full font-semibold text-xs flex items-center justify-center tracking-wider shadow-sm"
                             style="background:{{ $bgs[$idx] }}; color:{{ $texts[$idx] }};">
                            {{ $m->initials ?? strtoupper(substr($m->first_name ?? '', 0, 1)) }}
                        </div>
                    </td>
                    <td class="px-4 py-3.5 font-medium text-slate-600 whitespace-nowrap">{{ $m->title ?? '—' }}</td>
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        <p class="m-0 font-semibold text-slate-800">{{ $m->first_name }} {{ $m->last_name }}</p>
                    </td>
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        <div class="text-sm text-slate-700 font-medium">{{ $m->email }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $m->phone }}</div>
                    </td>
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        <span class="px-3 py-1 rounded-full text-xs bg-slate-100 text-slate-600 font-medium border border-slate-200/40">
                            {{ $m->cell ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-slate-600 whitespace-nowrap font-medium">{{ $m->church ?? '—' }}</td>
                    <td class="px-4 py-3.5 font-bold text-blue-600 whitespace-nowrap text-sm">{{ $attCount }}×</td>
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border"
                              style="background:{{ $present ? '#ecfdf5' : '#fef2f2' }};
                                     color:{{ $present ? '#166534' : '#991b1b' }};
                                     border-color:{{ $present ? '#bbf7d0' : '#fecaca' }};">
                            {{ $present ? '✓ Present' : '✗ Absent' }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 whitespace-nowrap">
                        <div class="flex gap-2">
                            <button onclick="openEdit({{ $m->toJson() }})"
                                    class="btn-outline px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                                ✎ Edit
                            </button>
                            <form method="POST" action="{{ route('admin.members.destroy', $m) }}" class="inline" id="delete-form-{{ $m->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                        onclick="confirmDelete({{ $m->id }}, '{{ $m->first_name }} {{ $m->last_name }}')"
                                        class="btn-outline px-3 py-1.5 rounded-lg border text-xs font-medium text-red-600 bg-red-50/30 border-red-200 hover:bg-red-50/80 transition-all shadow-sm">
                                    🗑 Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-20 text-center text-slate-400 font-medium">
                        No members found matched your directory parameters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination UI --}}
    <div class="px-6 py-4 flex flex-col sm:flex-row gap-4 justify-between items-center border-t border-slate-200 bg-slate-50/50">
        <span class="text-sm text-slate-500 font-medium">{{ $members->total() }} members • Page {{ $members->currentPage() }} of {{ $members->lastPage() }}</span>
        <div class="flex gap-2">
            @if($members->onFirstPage())
                <span class="btn-outline text-xs px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200">← Prev</span>
            @else
                <a href="{{ $members->previousPageUrl() }}" class="btn-outline text-xs px-3 py-1.5 rounded-lg bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 transition-colors">← Prev</a>
            @endif

            @if($members->hasMorePages())
                <a href="{{ $members->nextPageUrl() }}" class="btn-outline text-xs px-3 py-1.5 rounded-lg bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 transition-colors">Next →</a>
            @else
                <span class="btn-outline text-xs px-3 py-1.5 rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200">Next →</span>
            @endif
        </div>
    </div>
</div>

{{-- Add Member Modal --}}
<div id="modal-add" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl p-6 md:p-8 w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl transition-all" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl md:text-2xl text-slate-800 font-semibold">Add New Member</h2>
            <button onclick="closeModal('modal-add')" class="text-slate-400 hover:text-slate-600 font-bold p-1 text-lg">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.members.store') }}">
            @csrf
            @include('admin._member_form')
        </form>
    </div>
</div>

{{-- Edit Member Modal --}}
<div id="modal-edit" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl p-6 md:p-8 w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl transition-all" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl md:text-2xl text-slate-800 font-semibold">Edit Member</h2>
            <button onclick="closeModal('modal-edit')" class="text-slate-400 hover:text-slate-600 font-bold p-1 text-lg">&times;</button>
        </div>
        <form method="POST" id="edit-form" action="">
            @csrf @method('PUT')
            @include('admin._member_form', ['editing' => true])
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="delete-confirm-modal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
    <div class="bg-white rounded-2xl p-6 md:p-8 w-full max-w-md shadow-2xl transition-all" onclick="event.stopPropagation()">
        <h3 class="font-semibold text-slate-800 text-lg md:text-xl mb-2">Delete Member?</h3>
        <p id="delete-member-name" class="m-0 mb-6 text-slate-500 text-sm leading-relaxed"></p>
        <div class="flex gap-3 justify-end">
            <button onclick="closeDeleteModal()"
                class="px-4 py-2.5 border border-blue-200 rounded-lg bg-white text-blue-700 text-sm font-medium hover:bg-blue-50/50 transition-colors cursor-pointer">
                Cancel
            </button>
            <button onclick="executeDelete()"
                class="px-4 py-2.5 border-none rounded-lg bg-red-500 text-white text-sm font-semibold hover:bg-red-600 transition-colors cursor-pointer shadow-md shadow-red-500/20">
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
    document.getElementById('delete-member-name').textContent = `Are you sure you want to permanently delete ${name}? This operational step cannot be reversed.`;
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
