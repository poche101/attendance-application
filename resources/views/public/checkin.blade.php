@extends('layouts.app')
@section('title', 'Sunday Service Check-in')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
<style>
:root {
  --ink:        #0f172a;
  --paper:      #f8fafc;
  --cream:      #f1f5f9;
  --border:     #e2e8f0;
  --muted:      #64748b;
  --white:      #ffffff;
  --accent:     #1e40af;
  --accent-2:   #3b82f6;
  --accent-soft:#dbeafe;
  --radius:     14px;
  --radius-sm:  8px;
  --radius-lg:  22px;
  --shadow:     0 4px 20px rgba(15,23,42,.08);
  --font-head:  'Syne', sans-serif;
  --font-body:  'DM Sans', sans-serif;
  --max-w:      440px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: var(--font-body);
  background: var(--paper);
  color: var(--ink);
  min-height: 100vh;
  overflow-x: hidden;
}
a { color: inherit; text-decoration: none; }
button, input, select { font-family: var(--font-body); }
input:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
button:focus-visible { outline: 2px solid var(--accent); outline-offset: 3px; }

.checkin-page { min-height: 100vh; display: flex; flex-direction: column; }

/* ── Hero ── */
.checkin-hero { position: relative; background: var(--ink); color: white; overflow: hidden; padding: 0 24px; }
.checkin-hero::before {
  content: ''; position: absolute; inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E");
  pointer-events: none; z-index: 0;
}
.checkin-hero::after {
  content: ''; position: absolute; width: 300px; height: 300px;
  background: radial-gradient(circle, var(--accent) 0%, transparent 70%);
  top: -100px; right: -80px; opacity: .16; pointer-events: none; z-index: 0;
}
.hero-blob {
  position: absolute; width: 220px; height: 220px;
  background: radial-gradient(circle, var(--accent-2) 0%, transparent 70%);
  bottom: -70px; left: -50px; opacity: .13; pointer-events: none; z-index: 0;
}
.hero-nav {
  position: relative; z-index: 1; display: flex; align-items: center;
  justify-content: space-between; padding: 20px 0 16px;
  border-bottom: 1px solid rgba(255,255,255,.1);
  max-width: var(--max-w); margin: 0 auto; width: 100%;
}
.hero-date-badge {
  font-size: .68rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
  background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
  color: var(--accent-2); padding: 5px 11px; border-radius: 99px;
}
.hero-body {
  position: relative; z-index: 1; max-width: var(--max-w); margin: 0 auto;
  padding: 36px 0 40px; display: flex; flex-direction: column; align-items: center; text-align: center;
}
.hero-label {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: .7rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase;
  color: var(--accent-2); margin-bottom: 14px;
}
.hero-label::before {
  content: ''; display: inline-block; width: 6px; height: 6px;
  background: var(--accent-2); border-radius: 50%; animation: pulse-dot 2s infinite;
}
@keyframes pulse-dot {
  0%,100% { opacity:1; transform:scale(1); }
  50%      { opacity:.35; transform:scale(1.5); }
}
.hero-title {
  font-family: var(--font-head); font-size: clamp(2rem, 9vw, 2.7rem);
  font-weight: 800; line-height: 1.08; letter-spacing: -.03em; color: white; margin-bottom: 12px;
}
.hero-title em { font-style: italic; color: var(--accent-2); }
.hero-sub { font-size: .9rem; line-height: 1.6; color: rgba(255,255,255,.75); max-width: 320px; margin-bottom: 28px; }
.qr-tile {
  width: 148px; height: 148px; border-radius: var(--radius);
  background: rgba(219,234,254,.1); border: 1.5px dashed rgba(59,130,246,.4);
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;
}
.qr-grid { display: grid; gap: 2px; grid-template-columns: repeat(7, 11px); }
.qr-cell { width: 11px; height: 11px; border-radius: 2px; }
.qr-caption { font-size: .6rem; letter-spacing: .06em; text-transform: uppercase; color: rgba(59,130,246,.7); }

/* ── Main ── */
.checkin-main { flex: 1; display: flex; flex-direction: column; align-items: center; padding: 32px 24px 48px; }
.checkin-card-wrap { width: 100%; max-width: var(--max-w); }

/* ── State cards ── */
.state-card {
  background: var(--white); border: 1.5px solid var(--border); border-radius: var(--radius-lg);
  padding: 36px 28px; text-align: center; box-shadow: var(--shadow);
  animation: card-in .35s cubic-bezier(.34,1.3,.64,1) both;
}
@keyframes card-in {
  from { opacity:0; transform:translateY(14px) scale(.98); }
  to   { opacity:1; transform:translateY(0) scale(1); }
}
.state-icon-wrap {
  width: 100px; height: 100px; border-radius: 50%; display: flex;
  align-items: center; justify-content: center; margin: 0 auto 22px;
  box-shadow: 0 8px 32px rgba(30,64,175,.12);
}
.state-icon-wrap.warm  { background: linear-gradient(135deg, #dbeafe, #bfdbfe); }
.state-icon-wrap.amber { background: linear-gradient(135deg, #bfdbfe, #93c5fd); }
.state-icon-wrap.red   { background: linear-gradient(135deg, #fee2e2, #fecaca); }
.state-heading {
  font-family: var(--font-head); font-size: 1.55rem; font-weight: 800;
  letter-spacing: -.025em; color: var(--ink); line-height: 1.15; margin-bottom: 8px;
}
.state-member { font-size: .88rem; font-weight: 600; color: var(--accent); margin-bottom: 4px; }
.state-body { font-size: .85rem; line-height: 1.55; color: var(--muted); margin-top: 6px; }
.state-body.error-text { color: #991b1b; font-weight: 500; margin-bottom: 16px; }
.state-btn {
  display: inline-block; margin-top: 22px; padding: 11px 30px;
  background: var(--accent); color: #fff; font-family: var(--font-head);
  font-size: .88rem; font-weight: 700; letter-spacing: .03em;
  border-radius: var(--radius-sm); border: none; cursor: pointer;
  transition: background .18s, transform .12s, box-shadow .18s;
  box-shadow: 0 4px 18px rgba(30,64,175,.3); text-decoration: none;
}
.state-btn:hover  { background: #1e3a8a; box-shadow: 0 6px 24px rgba(30,64,175,.42); }
.state-btn:active { transform: scale(.97); }
.btn-outline-terracotta {
  display: inline-block; padding: 10px 28px; border: 1.5px solid var(--accent);
  color: var(--accent); font-family: var(--font-head); font-size: .86rem; font-weight: 700;
  border-radius: var(--radius-sm); cursor: pointer; transition: background .18s, color .18s; text-decoration: none;
}
.btn-outline-terracotta:hover { background: var(--accent); color: #fff; }
.register-link {
  display: inline-block; margin-top: 12px; font-size: .82rem; color: var(--accent);
  font-weight: 600; cursor: pointer; background: none; border: none;
  text-decoration: underline; text-underline-offset: 3px; font-family: var(--font-body);
}
.register-link:hover { color: #1e3a8a; }

/* ── Check-in form card ── */
.form-card {
  background: var(--white); border: 1.5px solid var(--border); border-radius: var(--radius-lg);
  padding: 28px 24px; box-shadow: var(--shadow); animation: card-in .35s cubic-bezier(.34,1.3,.64,1) both;
}
.field-label {
  display: block; font-size: .7rem; font-weight: 600; letter-spacing: .1em;
  text-transform: uppercase; color: var(--accent); margin-bottom: 7px;
}
.field-input {
  width: 100%; background: white; border: 1.5px solid var(--border); border-radius: var(--radius-sm);
  padding: 12px 14px; font-size: .92rem; font-family: var(--font-body); color: var(--ink);
  transition: border-color .2s, background .2s; margin-bottom: 14px;
}
.field-input::placeholder { color: var(--muted); opacity: .7; }
.field-input:focus-visible { outline: none; border-color: var(--accent); background: var(--white); }
.error-msg { font-size: .78rem; color: #991b1b; margin: -8px 0 12px; font-weight: 500; }
.submit-btn {
  width: 100%; padding: 14px; background: var(--accent); color: #fff;
  font-family: var(--font-head); font-size: 1rem; font-weight: 700; letter-spacing: .02em;
  border: none; border-radius: var(--radius); cursor: pointer;
  transition: background .18s, box-shadow .18s, transform .12s;
  box-shadow: 0 4px 18px rgba(30,64,175,.32);
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.submit-btn:hover  { background: #1e3a8a; box-shadow: 0 6px 26px rgba(30,64,175,.44); }
.submit-btn:active { transform: scale(.98); }

.checkin-hint { margin-top: 20px; font-size: .75rem; color: var(--muted); text-align: center; line-height: 1.5; }

.checkin-footer { background: var(--cream); border-top: 1.5px solid var(--border); padding: 16px 24px; text-align: center; }
.checkin-footer p { font-size: .72rem; color: var(--muted); letter-spacing: .03em; }
.checkin-footer strong { color: var(--ink); }

/* ── Toast notification ── */
.toast {
  position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-80px);
  background: #0f172a; color: #fff; padding: 12px 22px; border-radius: 10px;
  font-size: .84rem; font-weight: 500; z-index: 999;
  box-shadow: 0 8px 32px rgba(15,23,42,.25);
  transition: transform .35s cubic-bezier(.34,1.3,.64,1), opacity .3s;
  opacity: 0; white-space: nowrap;
}
.toast.toast-success { background: #166534; }
.toast.toast-error   { background: #991b1b; }
.toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }

/* ── Registration Modal ── */
.reg-modal-backdrop {
  display: none; position: fixed; inset: 0;
  background: rgba(15,23,42,.55); z-index: 200;
  align-items: flex-end; justify-content: center;
}
.reg-modal-backdrop.open { display: flex; }

.reg-modal-sheet {
  background: var(--white); border-radius: 24px 24px 0 0; width: 100%;
  max-width: 480px; max-height: 92vh; overflow-y: auto; padding: 24px 24px 36px;
  animation: sdin .32s cubic-bezier(.34,1.25,.64,1) both;
}
@keyframes sdin  { from { transform:translateY(100%); } to { transform:translateY(0); } }
@keyframes sdout { from { transform:translateY(0); } to { transform:translateY(100%); } }
.reg-modal-sheet.closing { animation: sdout .22s ease both; }

.modal-handle { width: 40px; height: 4px; background: var(--border); border-radius: 99px; margin: 0 auto 20px; }
.modal-title { font-family: var(--font-head); font-size: 1.3rem; font-weight: 800; letter-spacing: -.02em; color: var(--ink); margin-bottom: 4px; }
.modal-sub { font-size: .8rem; color: var(--muted); margin-bottom: 22px; line-height: 1.5; }

.reg-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.reg-field { display: flex; flex-direction: column; gap: 5px; }
.reg-field.full { grid-column: 1 / -1; }
.reg-label { font-size: .68rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--accent); }
.reg-label .req { color: #ef4444; }
.reg-input {
  width: 100%; border: 1.5px solid #bfdbfe; border-radius: 8px; padding: 11px 14px;
  font-size: .88rem; background: #f8faff; color: var(--ink); font-family: var(--font-body);
  outline: none; transition: border-color .2s, box-shadow .2s;
}
.reg-input:focus { border-color: var(--accent-2); box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
.reg-input.is-invalid { border-color: #fca5a5; background: #fff5f5; }
select.reg-input {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231E40AF' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 14px center; background-color: #f8faff; cursor: pointer;
}
.reg-error { font-size: .72rem; color: #991b1b; font-weight: 500; margin-top: 2px; }

.modal-divider { border-top: 1.5px solid var(--border); margin: 22px 0 18px; }
.modal-btn-row { display: flex; gap: 10px; justify-content: flex-end; align-items: center; }
.modal-btn-cancel {
  padding: 10px 22px; border: 1.5px solid #bfdbfe; border-radius: 8px;
  background: var(--white); color: var(--accent); font-family: var(--font-body);
  font-size: .86rem; font-weight: 500; cursor: pointer; transition: background .18s, border-color .18s;
}
.modal-btn-cancel:hover { background: #eff6ff; border-color: #93c5fd; }
.modal-btn-save {
  padding: 10px 28px; border: none; border-radius: 8px; background: var(--accent);
  color: var(--white); font-family: var(--font-head); font-size: .86rem; font-weight: 700;
  letter-spacing: .03em; cursor: pointer; box-shadow: 0 4px 14px rgba(30,64,175,.28);
  transition: background .18s, box-shadow .18s, transform .1s;
  display: flex; align-items: center; gap: 8px;
}
.modal-btn-save:hover  { background: #1e3a8a; box-shadow: 0 6px 20px rgba(30,64,175,.38); }
.modal-btn-save:active { transform: scale(.98); }
.modal-btn-save:disabled { opacity: .65; cursor: not-allowed; }
.spinner {
  width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.4);
  border-top-color: #fff; border-radius: 50%; animation: spin .7s linear infinite; display: none;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="checkin-page">

  {{-- ═══ HERO ═══ --}}
  <header class="checkin-hero">
    <div class="hero-blob" aria-hidden="true"></div>
    <nav class="hero-nav">
      <span class="hero-date-badge">{{ now()->format('D, d M Y') }}</span>
    </nav>
    <div class="hero-body">
      <p class="hero-label" aria-hidden="true">Sunday Service · Check-in</p>
      <h1 class="hero-title">Welcome to<br><em>Church</em></h1>
      <p class="hero-sub">Mark your attendance by entering your email address below</p>
      <div class="qr-tile" role="img" aria-label="QR code – scan or enter email">
        @php $on = [0,1,2,3,4,5,6,7,14,21,28,35,42,43,44,45,46,47,48,6,13,20,27,34,41,3,10,17,24,38]; @endphp
        <div class="qr-grid" aria-hidden="true">
          @for($i=0;$i<49;$i++)
            <div class="qr-cell" style="background:{{ in_array($i,$on) ? '#1e40af' : 'rgba(59,130,246,0.18)' }};"></div>
          @endfor
        </div>
        <span class="qr-caption">Scan or Enter Email</span>
      </div>
    </div>
  </header>

  {{-- ═══ MAIN ═══ --}}
  <main class="checkin-main">
    <div class="checkin-card-wrap">

      @if(session('status') === 'error')
        <div class="state-card" role="alert">
          <p class="state-body error-text">Something went wrong. Please try again or contact an administrator.</p>
          <a href="{{ route('checkin') }}" class="btn-outline-terracotta">Try Again</a>
        </div>

      @elseif(session('status') === 'success')
        <div class="state-card" role="status">
          <div class="state-icon-wrap warm" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 24 24" fill="#1e40af">
              <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/>
              <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3" fill="#93c5fd"/>
            </svg>
          </div>
          <h2 class="state-heading">Attendance Updated<br>Successfully!</h2>
          @if(session('member_name'))
            <p class="state-member">{{ session('member_name') }}</p>
          @endif
          <p class="state-body">Your attendance has been recorded. God bless you.</p>
          <a href="{{ route('checkin') }}" class="state-btn">Mark Another →</a>
        </div>

      @elseif(session('status') === 'duplicate')
        <div class="state-card" role="status">
          <div class="state-icon-wrap amber" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                 stroke="#3b82f6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
          </div>
          <h2 class="state-heading">Already Checked In!</h2>
          @if(session('member_name'))
            <p class="state-member">{{ session('member_name') }}</p>
          @endif
          <p class="state-body">You're already marked present for today's service.</p>
          <a href="{{ route('checkin') }}" class="state-btn">Mark Another →</a>
        </div>

      @elseif(session('status') === 'pending_activation')
        <div class="state-card" role="alert">
          <div class="state-icon-wrap amber" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                 stroke="#3b82f6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
          </div>
          <h2 class="state-heading">Account Pending</h2>
          @if(session('member_name'))
            <p class="state-member">{{ session('member_name') }}</p>
          @endif
          <p class="state-body">
            Your registration is received, but your profile isn't active yet.<br>
            Please wait a moment for an administrator to approve it.
          </p>
          <a href="{{ route('checkin') }}" class="state-btn">Back to Check-in →</a>
        </div>

      @elseif(session('status') === 'not_found')
        <div class="state-card" role="alert">
          <div class="state-icon-wrap red" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                 stroke="#991b1b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <line x1="15" y1="9" x2="9" y2="15"/>
              <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
          </div>
          <h2 class="state-heading">Email Not Found</h2>
          <p class="state-body">
            We couldn't find <strong style="color:var(--ink);">{{ session('attempted_email') }}</strong> in our records.<br>
            Not registered yet?
          </p>
          <a href="{{ route('checkin') }}" class="state-btn">Try Again →</a>
          <br>
          <button type="button" class="register-link" onclick="openRegModal()">
            Register as a new member →
          </button>
        </div>

      @elseif(session('status') === 'registered')
        <div class="state-card" role="status">
          <div class="state-icon-wrap warm" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 24 24" fill="none"
                 stroke="#1e40af" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <line x1="19" y1="8" x2="19" y2="14"/>
              <line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
          </div>
          <h2 class="state-heading">Registration Submitted!</h2>
          <p class="state-body">
            Your details have been received.<br>
            An administrator will activate your account shortly. God bless you.
          </p>
          <a href="{{ route('checkin') }}" class="state-btn">Back to Check-in →</a>
        </div>

      @else
        <div class="form-card">
          <form method="POST" action="{{ route('checkin.store') }}">
            @csrf
            <label for="email" class="field-label">Your Email Address</label>
            <input
              type="email" id="email" name="email"
              placeholder="yourname@example.com"
              class="field-input" value="{{ old('email') }}"
              required autocomplete="email" aria-required="true"
            />
            @error('email')
              <p class="error-msg" role="alert">{{ $message }}</p>
            @enderror
            <button type="submit" class="submit-btn">
              Mark My Attendance <span aria-hidden="true">→</span>
            </button>
          </form>
        </div>
      @endif

      <p class="checkin-hint">
        If your email isn't recognised, you can register below or speak with an administrator.
      </p>
    </div>
  </main>

  <footer class="checkin-footer">
    <p>© {{ now()->year }} <strong>Sunday Service</strong> · Attendance System</p>
  </footer>
</div>

{{-- ═══ TOAST ═══ --}}
<div class="toast" id="toast" role="status" aria-live="polite"></div>

{{-- ═══ REGISTRATION MODAL ═══ --}}
<div class="reg-modal-backdrop" id="regModalBackdrop" role="dialog" aria-modal="true" aria-labelledby="regModalTitle">
  <div class="reg-modal-sheet" id="regModalSheet">
    <div class="modal-handle" aria-hidden="true"></div>

    <h2 class="modal-title" id="regModalTitle">New Member Registration</h2>
    <p class="modal-sub">Fill in your details and tap Register. An administrator will activate your account before you can check in.</p>

    <form method="POST" action="{{ route('members.store') }}" id="regForm" novalidate>
      @csrf

      <div class="reg-grid">

        {{-- Title --}}
        <div class="reg-field full">
          <label class="reg-label" for="reg_title">Title</label>
          <select name="title" id="reg_title" class="reg-input">
            <option value="">— Select Title —</option>
            @foreach(['Bro','Sis','Pastor','Deacon','Deaconess','Mr','Mrs','Miss','Brother','Sister'] as $t)
              <option value="{{ $t }}" {{ old('title') === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
          </select>
        </div>

        {{-- First Name --}}
        <div class="reg-field">
          <label class="reg-label" for="reg_first_name">First Name <span class="req">*</span></label>
          <input type="text" name="first_name" id="reg_first_name" required
            value="{{ old('first_name') }}" placeholder="e.g. John"
            class="reg-input @error('first_name') is-invalid @enderror">
          @error('first_name')
            <span class="reg-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- Last Name --}}
        <div class="reg-field">
          <label class="reg-label" for="reg_last_name">Last Name <span class="req">*</span></label>
          <input type="text" name="last_name" id="reg_last_name" required
            value="{{ old('last_name') }}" placeholder="e.g. Doe"
            class="reg-input @error('last_name') is-invalid @enderror">
          @error('last_name')
            <span class="reg-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- Email --}}
        <div class="reg-field full">
          <label class="reg-label" for="reg_email">Email <span class="req">*</span></label>
          <input type="email" name="email" id="reg_email" required
            value="{{ old('email', session('attempted_email')) }}"
            placeholder="e.g. john@example.com"
            class="reg-input @error('email') is-invalid @enderror">
          @error('email')
            <span class="reg-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- Phone --}}
        <div class="reg-field">
          <label class="reg-label" for="reg_phone">Phone</label>
          <input type="text" name="phone" id="reg_phone"
            value="{{ old('phone') }}" placeholder="e.g. 08012345678"
            class="reg-input @error('phone') is-invalid @enderror">
          @error('phone')
            <span class="reg-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- Group --}}
        <div class="reg-field">
          <label class="reg-label" for="reg_group">Group</label>
          <input type="text" name="group" id="reg_group"
            value="{{ old('group') }}" placeholder="e.g. Youth, Men, Choir"
            class="reg-input">
        </div>

        {{-- Church --}}
        <div class="reg-field full">
          <label class="reg-label" for="reg_church">Church</label>
          <input type="text" name="church" id="reg_church"
            value="{{ old('church') }}" placeholder="e.g. CE Lekki"
            class="reg-input">
        </div>

        {{-- Cell --}}
        <div class="reg-field">
          <label class="reg-label" for="reg_cell">Cell</label>
          <input type="text" name="cell" id="reg_cell"
            value="{{ old('cell') }}" placeholder="e.g. Cell 5"
            class="reg-input">
        </div>

        {{-- Birthday --}}
        <div class="reg-field">
          <label class="reg-label" for="reg_birthday">Birthday</label>
          <input type="date" name="birthday" id="reg_birthday"
            value="{{ old('birthday') }}"
            class="reg-input">
        </div>

      </div>

      <div class="modal-divider"></div>

      <div class="modal-btn-row">
        <button type="button" class="modal-btn-cancel" onclick="closeRegModal()">Cancel</button>
        <button type="submit" class="modal-btn-save" id="regSubmitBtn">
          <span id="regBtnText">Register Member</span>
          <span class="spinner" id="regSpinner"></span>
        </button>
      </div>
    </form>

  </div>
</div>

@push('scripts')
<script>
const backdrop = document.getElementById('regModalBackdrop');
const sheet    = document.getElementById('regModalSheet');

function openRegModal() {
  backdrop.classList.add('open');
  sheet.classList.remove('closing');
  document.body.style.overflow = 'hidden';
  // Focus first empty required field
  setTimeout(() => {
    const first = sheet.querySelector('input:not([type=hidden])[required]');
    if (first) first.focus();
  }, 350);
}

function closeRegModal() {
  sheet.classList.add('closing');
  setTimeout(() => {
    backdrop.classList.remove('open');
    sheet.classList.remove('closing');
    document.body.style.overflow = '';
  }, 230);
}

// Close on backdrop click
backdrop.addEventListener('click', e => { if (e.target === backdrop) closeRegModal(); });

// Show spinner on submit
document.getElementById('regForm').addEventListener('submit', function() {
  const btn     = document.getElementById('regSubmitBtn');
  const spinner = document.getElementById('regSpinner');
  const text    = document.getElementById('regBtnText');
  btn.disabled  = true;
  spinner.style.display = 'block';
  text.textContent = 'Registering…';
});

// Auto-open modal if validation errors exist (form was submitted but failed)
@if($errors->any())
  document.addEventListener('DOMContentLoaded', openRegModal);
@endif

// ── Toast helper ──
function showToast(message, type = 'success') {
  const toast = document.getElementById('toast');
  toast.textContent = message;
  toast.className   = 'toast toast-' + type + ' show';
  setTimeout(() => { toast.classList.remove('show'); }, 4000);
}

// Show toast for session flash messages
@if(session('status') === 'registered')
  document.addEventListener('DOMContentLoaded', () => showToast('Registration submitted successfully!', 'success'));
@elseif(session('status') === 'success')
  document.addEventListener('DOMContentLoaded', () => showToast('Attendance marked! God bless you.', 'success'));
@elseif(session('status') === 'duplicate')
  document.addEventListener('DOMContentLoaded', () => showToast('Already checked in today.', 'error'));
@elseif(session('status') === 'pending_activation')
  document.addEventListener('DOMContentLoaded', () => showToast('Profile review pending approval.', 'error'));
@elseif(session('status') === 'error')
  document.addEventListener('DOMContentLoaded', () => showToast('Something went wrong. Please try again.', 'error'));
@endif
</script>
@endpush
@endsection
