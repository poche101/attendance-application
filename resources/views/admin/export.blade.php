@extends('layouts.admin')
@section('title', 'Export')

@section('main')

{{-- Header --}}
<div style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:28px;">
    <div>
        <span style="font-size:12px; letter-spacing:0.12em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif;">Reports</span>
        <h2 class="font-head" style="font-size:32px; margin:4px 0 0; color:#0F172A; font-weight:600;">Export Attendance Data</h2>
    </div>
</div>

<div style="max-width:580px;">

    {{-- Main export card --}}
    <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:16px; padding:32px;">

        <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
            <div style="width:40px; height:40px; background:#DBEAFE; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">↓</div>
            <div>
                <h3 class="font-head" style="margin:0; font-size:20px; color:#0F172A; font-weight:600;">Configure Export</h3>
                <p style="margin:2px 0 0; font-size:12px; color:#64748B; font-family:'DM Sans',sans-serif;">Choose your date range and filters below</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.export.csv') }}">

            {{-- Date range --}}
            <p style="margin:0 0 8px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif; font-weight:600;">Date Range</p>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px;">
                <div>
                    <label style="display:block; font-size:12px; color:#64748B; margin-bottom:5px; font-family:'DM Sans',sans-serif;">From Date</label>
                    <input type="date" name="from" value="2024-01-01"
                        style="width:100%; border:1.5px solid #BFDBFE; border-radius:8px; padding:10px 12px; font-size:13px; background:#F8FAFF; color:#0F172A; font-family:'DM Sans',sans-serif; outline:none; transition:border-color .2s;"
                        onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#BFDBFE'">
                </div>
                <div>
                    <label style="display:block; font-size:12px; color:#64748B; margin-bottom:5px; font-family:'DM Sans',sans-serif;">To Date</label>
                    <input type="date" name="to" value="{{ now()->toDateString() }}"
                        style="width:100%; border:1.5px solid #BFDBFE; border-radius:8px; padding:10px 12px; font-size:13px; background:#F8FAFF; color:#0F172A; font-family:'DM Sans',sans-serif; outline:none; transition:border-color .2s;"
                        onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#BFDBFE'">
                </div>
            </div>

            {{-- Filters --}}
            <p style="margin:0 0 8px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif; font-weight:600;">Filters</p>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:24px;">
                <div>
                    <label style="display:block; font-size:12px; color:#64748B; margin-bottom:5px; font-family:'DM Sans',sans-serif;">Filter by Group</label>
                    <select name="group"
                        style="width:100%; border:1.5px solid #BFDBFE; border-radius:8px; padding:10px 12px; font-size:13px; background:#F8FAFF; color:#0F172A; font-family:'DM Sans',sans-serif; outline:none; transition:border-color .2s;"
                        onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#BFDBFE'">
                        <option value="">All Groups</option>
                        @foreach(['Youth','Men','Women','Choir'] as $g)
                        <option value="{{ $g }}">{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:12px; color:#64748B; margin-bottom:5px; font-family:'DM Sans',sans-serif;">Filter by Church</label>
                    <select name="church"
                        style="width:100%; border:1.5px solid #BFDBFE; border-radius:8px; padding:10px 12px; font-size:13px; background:#F8FAFF; color:#0F172A; font-family:'DM Sans',sans-serif; outline:none; transition:border-color .2s;"
                        onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#BFDBFE'">
                        <option value="">All Churches</option>
                        @foreach($churches as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Fields included --}}
            <div style="background:#EFF6FF; border:1.5px solid #BFDBFE; border-radius:10px; padding:16px 18px; margin-bottom:24px;">
                <p style="margin:0 0 10px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif; font-weight:600;">
                    Fields included in export
                </p>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @foreach(['First Name','Last Name','Email','Phone','Group','Church','Cell','Birthday','Attendance Status','Service','Date'] as $f)
                    <span style="display:inline-block; padding:3px 10px; border-radius:9999px; font-size:11px; font-weight:500; background:#fff; color:#1E40AF; border:1px solid #93C5FD; font-family:'DM Sans',sans-serif;">
                        {{ $f }}
                    </span>
                    @endforeach
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                style="width:100%; padding:13px; background:#1E40AF; color:#fff; font-family:'Syne',sans-serif; font-size:15px; font-weight:700; border:none; border-radius:10px; cursor:pointer; letter-spacing:0.02em; box-shadow:0 4px 18px rgba(30,64,175,.25); transition:background .18s;"
                onmouseover="this.style.background='#1E3A8A'" onmouseout="this.style.background='#1E40AF'">
                ↓ Export as CSV
            </button>
        </form>
    </div>

    {{-- Summary card --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-top:16px;">

        <div style="background:#fff; border:1.5px solid #93C5FD; border-radius:14px; padding:20px 22px;">
            <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif; font-weight:600;">Total Records</p>
            <p class="font-head" style="margin:0; font-size:36px; font-weight:700; color:#0F172A; line-height:1.1;">{{ $totalRecords }}</p>
            <p style="margin:4px 0 0; font-size:12px; color:#64748B; font-family:'DM Sans',sans-serif;">attendance entries</p>
        </div>

        <div style="background:#EFF6FF; border:1.5px solid #93C5FD; border-radius:14px; padding:20px 22px;">
            <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; font-family:'DM Sans',sans-serif; font-weight:600;">Active Members</p>
            <p class="font-head" style="margin:0; font-size:36px; font-weight:700; color:#1E40AF; line-height:1.1;">{{ $totalMembers }}</p>
            <p style="margin:4px 0 0; font-size:12px; color:#64748B; font-family:'DM Sans',sans-serif;">Sunday Service</p>
        </div>

    </div>
</div>

@endsection
