@extends('layouts.admin')
@section('title', 'Export')

@section('main')
<div class="mb-7">
    <span class="text-xs tracking-[0.12em] uppercase" style="color:#B86A1A;">Reports</span>
    <h2 class="cg text-4xl mt-1" style="color:#1E1208; font-weight:500;">Export Attendance Data</h2>
</div>

<div style="max-width:560px;">
    <div class="bg-white border rounded-xl p-8" style="border-color:#FAD9B5;">
        <h3 class="cg mb-5" style="font-size:22px; color:#1E1208; font-weight:500;">Configure Export</h3>

        <form method="GET" action="{{ route('admin.export.csv') }}">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label class="form-label">From Date</label>
                    <input type="date" name="from" value="2024-01-01"
                        style="width:100%;border:1px solid #FAD9B5;border-radius:8px;padding:10px 14px;font-size:13px;background:#FFFBF5;color:#1E1208;font-family:'Jost',sans-serif;">
                </div>
                <div>
                    <label class="form-label">To Date</label>
                    <input type="date" name="to" value="{{ now()->toDateString() }}"
                        style="width:100%;border:1px solid #FAD9B5;border-radius:8px;padding:10px 14px;font-size:13px;background:#FFFBF5;color:#1E1208;font-family:'Jost',sans-serif;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
                <div>
                    <label class="form-label">Filter by Group</label>
                    <select name="group"
                        style="width:100%;border:1px solid #FAD9B5;border-radius:8px;padding:10px 14px;font-size:13px;background:#FFFBF5;color:#1E1208;font-family:'Jost',sans-serif;">
                        <option value="">All Groups</option>
                        @foreach(['Youth','Men','Women','Choir'] as $g)
                        <option value="{{ $g }}">{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Filter by Church</label>
                    <select name="church"
                        style="width:100%;border:1px solid #FAD9B5;border-radius:8px;padding:10px 14px;font-size:13px;background:#FFFBF5;color:#1E1208;font-family:'Jost',sans-serif;">
                        <option value="">All Churches</option>
                        @foreach($churches as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="background:#FFF5E8; border:1px solid #FAD9B5; border-radius:8px; padding:14px 18px; margin-bottom:24px;">
                <p style="margin:0 0 8px; font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">
                    Fields included in export
                </p>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @foreach(['First Name','Last Name','Email','Phone','Group','Church','Cell','Birthday','Attendance Status','Service','Date'] as $f)
                    <span class="badge" style="background:#fff; color:#C45E08; border:1px solid #F0A055; font-family:'Jost',sans-serif;">{{ $f }}</span>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn-primary w-full text-center" style="width:100%; padding:12px;">
                ↓ Export as CSV
            </button>
        </form>
    </div>

    <div class="bg-white border rounded-xl px-6 py-5 mt-5" style="border-color:#FAD9B5;">
        <p style="margin:0 0 4px; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#B86A1A; font-family:'Jost',sans-serif;">Summary</p>
        <p class="cg m-0" style="font-size:28px; color:#1E1208;">
            {{ $totalRecords }} <span style="font-size:16px; color:#B86A1A;">total records</span>
        </p>
        <p style="margin:4px 0 0; font-size:13px; color:#F0A055; font-family:'Jost',sans-serif;">
            {{ $totalMembers }} active members · Sunday Service
        </p>
    </div>
</div>
@endsection
