@php $editing = $editing ?? false; @endphp

<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

    {{-- Title --}}
    <div style="grid-column:1/-1;">
        <label style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif; font-weight:600;">Title</label>
        <select name="title" style="
            width:100%;
            border:1.5px solid #BFDBFE;
            border-radius:8px;
            padding:11px 14px;
            font-size:14px;
            background:#F8FAFF;
            color:#0F172A;
            font-family:'DM Sans',sans-serif;
            appearance:none;
            background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231E40AF' d='M6 8L1 3h10z'/%3E%3C/svg%3E\");
            background-repeat:no-repeat;
            background-position:right 14px center;
            cursor:pointer;
            outline:none;
            transition:border-color .2s, box-shadow .2s;"
            onfocus="this.style.borderColor='#3B82F6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
            onblur="this.style.borderColor='#BFDBFE';this.style.boxShadow='none'">
            <option value="">— Select Title —</option>
            @foreach(['Bro','Sis','Pastor','Deacon','Deaconess','Mr','Mrs','Miss','Brother','Sister'] as $t)
                <option value="{{ $t }}" {{ (old('title', $member->title ?? '') === $t) ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    {{-- First Name --}}
    <div>
        <label style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif; font-weight:600;">
            First Name <span style="color:#EF4444;">*</span>
        </label>
        <input type="text" name="first_name" required
            value="{{ old('first_name', $member->first_name ?? '') }}"
            placeholder="e.g. John"
            style="
                width:100%;
                border:1.5px solid #BFDBFE;
                border-radius:8px;
                padding:11px 14px;
                font-size:14px;
                background:#F8FAFF;
                color:#0F172A;
                font-family:'DM Sans',sans-serif;
                outline:none;
                transition:border-color .2s, box-shadow .2s;"
            onfocus="this.style.borderColor='#3B82F6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
            onblur="this.style.borderColor='#BFDBFE';this.style.boxShadow='none'">
    </div>

    {{-- Last Name --}}
    <div>
        <label style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif; font-weight:600;">
            Last Name <span style="color:#EF4444;">*</span>
        </label>
        <input type="text" name="last_name" required
            value="{{ old('last_name', $member->last_name ?? '') }}"
            placeholder="e.g. Doe"
            style="
                width:100%;
                border:1.5px solid #BFDBFE;
                border-radius:8px;
                padding:11px 14px;
                font-size:14px;
                background:#F8FAFF;
                color:#0F172A;
                font-family:'DM Sans',sans-serif;
                outline:none;
                transition:border-color .2s, box-shadow .2s;"
            onfocus="this.style.borderColor='#3B82F6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
            onblur="this.style.borderColor='#BFDBFE';this.style.boxShadow='none'">
    </div>

    {{-- Email --}}
    <div style="grid-column:1/-1;">
        <label style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif; font-weight:600;">
            Email <span style="color:#EF4444;">*</span>
        </label>
        <input type="email" name="email" required
            value="{{ old('email', $member->email ?? '') }}"
            placeholder="e.g. john@example.com"
            style="
                width:100%;
                border:1.5px solid #BFDBFE;
                border-radius:8px;
                padding:11px 14px;
                font-size:14px;
                background:#F8FAFF;
                color:#0F172A;
                font-family:'DM Sans',sans-serif;
                outline:none;
                transition:border-color .2s, box-shadow .2s;"
            onfocus="this.style.borderColor='#3B82F6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
            onblur="this.style.borderColor='#BFDBFE';this.style.boxShadow='none'">
    </div>

    {{-- Phone --}}
    <div>
        <label style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif; font-weight:600;">Phone</label>
        <input type="text" name="phone"
            value="{{ old('phone', $member->phone ?? '') }}"
            placeholder="e.g. 08012345678"
            style="
                width:100%;
                border:1.5px solid #BFDBFE;
                border-radius:8px;
                padding:11px 14px;
                font-size:14px;
                background:#F8FAFF;
                color:#0F172A;
                font-family:'DM Sans',sans-serif;
                outline:none;
                transition:border-color .2s, box-shadow .2s;"
            onfocus="this.style.borderColor='#3B82F6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
            onblur="this.style.borderColor='#BFDBFE';this.style.boxShadow='none'">
    </div>

    {{-- Group --}}
    <div>
        <label style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif; font-weight:600;">Group</label>
        <input type="text" name="group"
            value="{{ old('group', $member->group ?? '') }}"
            placeholder="e.g. Youth, Men, Women, Choir"
            style="
                width:100%;
                border:1.5px solid #BFDBFE;
                border-radius:8px;
                padding:11px 14px;
                font-size:14px;
                background:#F8FAFF;
                color:#0F172A;
                font-family:'DM Sans',sans-serif;
                outline:none;
                transition:border-color .2s, box-shadow .2s;"
            onfocus="this.style.borderColor='#3B82F6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
            onblur="this.style.borderColor='#BFDBFE';this.style.boxShadow='none'">
    </div>

    {{-- Church --}}
    <div style="grid-column:1/-1;">
        <label style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif; font-weight:600;">Church</label>
        <input type="text" name="church"
            value="{{ old('church', $member->church ?? '') }}"
            placeholder="e.g. CE Lekki"
            style="
                width:100%;
                border:1.5px solid #BFDBFE;
                border-radius:8px;
                padding:11px 14px;
                font-size:14px;
                background:#F8FAFF;
                color:#0F172A;
                font-family:'DM Sans',sans-serif;
                outline:none;
                transition:border-color .2s, box-shadow .2s;"
            onfocus="this.style.borderColor='#3B82F6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
            onblur="this.style.borderColor='#BFDBFE';this.style.boxShadow='none'">
    </div>

    {{-- Cell --}}
    <div>
        <label style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif; font-weight:600;">Cell</label>
        <input type="text" name="cell"
            value="{{ old('cell', $member->cell ?? '') }}"
            placeholder="e.g. Cell 5"
            style="
                width:100%;
                border:1.5px solid #BFDBFE;
                border-radius:8px;
                padding:11px 14px;
                font-size:14px;
                background:#F8FAFF;
                color:#0F172A;
                font-family:'DM Sans',sans-serif;
                outline:none;
                transition:border-color .2s, box-shadow .2s;"
            onfocus="this.style.borderColor='#3B82F6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
            onblur="this.style.borderColor='#BFDBFE';this.style.boxShadow='none'">
    </div>

    {{-- Birthday --}}
    <div>
        <label style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; color:#1E40AF; margin-bottom:6px; display:block; font-family:'DM Sans',sans-serif; font-weight:600;">Birthday</label>
        <input type="date" name="birthday"
            value="{{ old('birthday', isset($member->birthday) ? $member->birthday->format('Y-m-d') : '') }}"
            style="
                width:100%;
                border:1.5px solid #BFDBFE;
                border-radius:8px;
                padding:11px 14px;
                font-size:14px;
                background:#F8FAFF;
                color:#0F172A;
                font-family:'DM Sans',sans-serif;
                outline:none;
                transition:border-color .2s, box-shadow .2s;"
            onfocus="this.style.borderColor='#3B82F6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,.15)'"
            onblur="this.style.borderColor='#BFDBFE';this.style.boxShadow='none'">
    </div>

</div>

{{-- Divider --}}
<div style="border-top:1.5px solid #E2E8F0; margin:28px 0 24px;"></div>

{{-- Action buttons --}}
<div style="display:flex; gap:10px; justify-content:flex-end; align-items:center;">

    {{-- Cancel --}}
    <button type="button"
        onclick="closeModal(this.closest('[id^=modal]')?.id)"
        style="
            padding:10px 22px;
            border:1.5px solid #BFDBFE;
            border-radius:8px;
            background:#fff;
            color:#1E40AF;
            font-family:'DM Sans',sans-serif;
            font-size:14px;
            font-weight:500;
            cursor:pointer;
            transition:background .18s, border-color .18s;"
        onmouseover="this.style.background='#EFF6FF';this.style.borderColor='#93C5FD'"
        onmouseout="this.style.background='#fff';this.style.borderColor='#BFDBFE'">
        Cancel
    </button>

    {{-- Save --}}
    <button type="submit"
        style="
            padding:10px 28px;
            border:none;
            border-radius:8px;
            background:#1E40AF;
            color:#fff;
            font-family:'Syne',sans-serif;
            font-size:14px;
            font-weight:700;
            letter-spacing:0.03em;
            cursor:pointer;
            box-shadow:0 4px 14px rgba(30,64,175,.28);
            transition:background .18s, box-shadow .18s, transform .1s;"
        onmouseover="this.style.background='#1E3A8A';this.style.boxShadow='0 6px 20px rgba(30,64,175,.38)'"
        onmouseout="this.style.background='#1E40AF';this.style.boxShadow='0 4px 14px rgba(30,64,175,.28)'"
        onmousedown="this.style.transform='scale(.98)'"
        onmouseup="this.style.transform='scale(1)'">
        {{ $editing ? 'Save Changes' : 'Add Member' }}
    </button>

</div>
