@php $editing = $editing ?? false; @endphp
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

    {{-- Title --}}
    <div style="grid-column:1/-1;">
        <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; font-family:'Jost',sans-serif;">Title</label>
        <select name="title"
            style="width:100%; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:14px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif; appearance:none;">
            <option value="">— Select Title —</option>
            @foreach(['Bro', 'Sis','Pastor','Deacon','Deaconess'] as $t)
                <option value="{{ $t }}" {{ (old('title', $member->title ?? '') === $t) ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    {{-- First Name --}}
    <div>
        <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; font-family:'Jost',sans-serif;">First Name *</label>
        <input type="text" name="first_name" required
            value="{{ old('first_name', $member->first_name ?? '') }}"
            style="width:100%; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:14px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    </div>

    {{-- Last Name --}}
    <div>
        <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; font-family:'Jost',sans-serif;">Last Name *</label>
        <input type="text" name="last_name" required
            value="{{ old('last_name', $member->last_name ?? '') }}"
            style="width:100%; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:14px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    </div>

    {{-- Email --}}
    <div style="grid-column:1/-1;">
        <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; font-family:'Jost',sans-serif;">Email *</label>
        <input type="email" name="email" required
            value="{{ old('email', $member->email ?? '') }}"
            style="width:100%; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:14px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    </div>

    {{-- Phone --}}
    <div>
        <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; font-family:'Jost',sans-serif;">Phone</label>
        <input type="text" name="phone"
            value="{{ old('phone', $member->phone ?? '') }}"
            style="width:100%; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:14px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    </div>

    {{-- Group --}}
    <div>
        <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; font-family:'Jost',sans-serif;">Group</label>
        <input type="text" name="group" placeholder="e.g. Youth, Men, Women, Choir…"
            value="{{ old('group', $member->group ?? '') }}"
            style="width:100%; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:14px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    </div>

    {{-- Church --}}
    <div style="grid-column:1/-1;">
        <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; font-family:'Jost',sans-serif;">Church</label>
        <input type="text" name="church"
            value="{{ old('church', $member->church ?? '') }}"
            style="width:100%; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:14px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    </div>

    {{-- Cell --}}
    <div>
        <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; font-family:'Jost',sans-serif;">Cell</label>
        <input type="text" name="cell"
            value="{{ old('cell', $member->cell ?? '') }}"
            style="width:100%; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:14px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    </div>

    {{-- Birthday --}}
    <div>
        <label style="font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; font-family:'Jost',sans-serif;">Birthday</label>
        <input type="date" name="birthday"
            value="{{ old('birthday', isset($member->birthday) ? $member->birthday->format('Y-m-d') : '') }}"
            style="width:100%; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:14px; background:#FFFBF5; color:#1E1208; font-family:'Jost',sans-serif;">
    </div>

</div>
