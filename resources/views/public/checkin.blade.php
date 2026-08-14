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
html { font-size: 16px; }
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

/* ── Top Bar ── */
.checkin-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 24px;
  background: var(--ink);
  border-bottom: 1px solid rgba(255,255,255,.08);
  position: relative;
  z-index: 2;
}
.topbar-title {
  font-family: var(--font-head);
  font-size: 1rem;
  font-weight: 800;
  color: var(--white);
  letter-spacing: -.02em;
}
.topbar-date {
  font-size: .68rem;
  font-weight: 600;
  letter-spacing: .08em;
  text-transform: uppercase;
  background: rgba(255,255,255,.1);
  border: 1px solid rgba(255,255,255,.15);
  color: var(--accent-2);
  padding: 4px 10px;
  border-radius: 99px;
  white-space: nowrap;
}

/* ── Hero ── */
.checkin-hero {
  background: var(--ink);
  padding: 0 24px 0;
  position: relative;
  overflow: hidden;
}
.checkin-hero::after {
  content: '';
  position: absolute;
  width: 340px; height: 340px;
  background: radial-gradient(circle, var(--accent) 0%, transparent 70%);
  top: -120px; right: -80px;
  opacity: .18; pointer-events: none;
}
.hero-glow-bottom {
  position: absolute;
  width: 260px; height: 260px;
  background: radial-gradient(circle, var(--accent-2) 0%, transparent 70%);
  bottom: -90px; left: -60px;
  opacity: .12; pointer-events: none;
}
.hero-inner {
  position: relative; z-index: 1;
  max-width: var(--max-w);
  margin: 0 auto;
  padding: 40px 0 44px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}
.hero-live-badge {
  display: inline-flex; align-items: center; gap: 7px;
  font-size: .68rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
  color: var(--accent-2);
  background: rgba(59,130,246,.1);
  border: 1px solid rgba(59,130,246,.25);
  padding: 5px 13px; border-radius: 99px;
  margin-bottom: 20px;
}
.hero-live-badge::before {
  content: '';
  width: 6px; height: 6px;
  background: var(--accent-2);
  border-radius: 50%;
  animation: pulse-dot 2s infinite;
}
@keyframes pulse-dot {
  0%,100% { opacity:1; transform:scale(1); }
  50%      { opacity:.3; transform:scale(1.6); }
}
.hero-title {
  font-family: var(--font-head);
  font-size: clamp(1.8rem, 7vw, 2.8rem);
  font-weight: 800;
  line-height: 1.06;
  letter-spacing: -.035em;
  color: var(--white);
  margin-bottom: 14px;
}
.hero-title em { font-style: italic; color: var(--accent-2); }
.hero-sub {
  font-size: .88rem;
  line-height: 1.65;
  color: rgba(255,255,255,.65);
  max-width: 300px;
  margin-bottom: 32px;
}
.hero-wave { display: block; width: 100%; margin-top: -1px; line-height: 0; }
.hero-wave svg { display: block; width: 100%; }

/* ── Main ── */
.checkin-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 32px 16px 48px;
}
.checkin-card-wrap { width: 100%; max-width: var(--max-w); }

/* ── State cards ── */
.state-card {
  background: var(--white);
  border: 1.5px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 36px 24px;
  text-align: center;
  box-shadow: var(--shadow);
  animation: card-in .35s cubic-bezier(.34,1.3,.64,1) both;
}
@keyframes card-in {
  from { opacity:0; transform:translateY(14px) scale(.98); }
  to   { opacity:1; transform:translateY(0) scale(1); }
}
.state-icon-wrap {
  width: 90px; height: 90px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 22px;
  box-shadow: 0 8px 32px rgba(30,64,175,.12);
}
.state-icon-wrap.warm  { background: linear-gradient(135deg, #dbeafe, #bfdbfe); }
.state-icon-wrap.amber { background: linear-gradient(135deg, #bfdbfe, #93c5fd); }
.state-icon-wrap.red   { background: linear-gradient(135deg, #fee2e2, #fecaca); }
.state-heading {
  font-family: var(--font-head);
  font-size: clamp(1.25rem, 5vw, 1.55rem);
  font-weight: 800;
  letter-spacing: -.025em;
  color: var(--ink);
  line-height: 1.15;
  margin-bottom: 8px;
}
.state-member { font-size: .88rem; font-weight: 600; color: var(--accent); margin-bottom: 4px; }
.state-body { font-size: .85rem; line-height: 1.55; color: var(--muted); margin-top: 6px; }
.state-body.error-text { color: #991b1b; font-weight: 500; margin-bottom: 16px; }
.state-btn {
  display: inline-block;
  margin-top: 22px;
  padding: 12px 30px;
  background: var(--accent);
  color: #fff;
  font-family: var(--font-head);
  font-size: .88rem;
  font-weight: 700;
  letter-spacing: .03em;
  border-radius: var(--radius-sm);
  border: none;
  cursor: pointer;
  transition: background .18s, transform .12s, box-shadow .18s;
  box-shadow: 0 4px 18px rgba(30,64,175,.3);
  text-decoration: none;
}
.state-btn:hover  { background: #1e3a8a; box-shadow: 0 6px 24px rgba(30,64,175,.42); }
.state-btn:active { transform: scale(.97); }

/* ── Not-found action group ── */
.not-found-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 22px;
  align-items: center;
}
.not-found-actions .state-btn { margin-top: 0; }

.action-divider {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  color: var(--muted);
  font-size: .72rem;
  font-weight: 500;
  letter-spacing: .05em;
}
.action-divider::before,
.action-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border);
}

.phone-alt-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 12px 20px;
  background: transparent;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--ink);
  font-family: var(--font-body);
  font-size: .86rem;
  font-weight: 600;
  cursor: pointer;
  transition: border-color .2s, background .2s, color .2s;
  text-decoration: none;
}
.phone-alt-btn:hover {
  border-color: var(--accent-2);
  background: var(--accent-soft);
  color: var(--accent);
}

.register-link {
  display: inline-block;
  margin-top: 4px;
  font-size: .82rem;
  color: var(--accent);
  font-weight: 600;
  cursor: pointer;
  background: none;
  border: none;
  text-decoration: underline;
  text-underline-offset: 3px;
  font-family: var(--font-body);
}
.register-link:hover { color: #1e3a8a; }

.btn-outline-terracotta {
  display: inline-block;
  padding: 10px 28px;
  border: 1.5px solid var(--accent);
  color: var(--accent);
  font-family: var(--font-head);
  font-size: .86rem;
  font-weight: 700;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: background .18s, color .18s;
  text-decoration: none;
}
.btn-outline-terracotta:hover { background: var(--accent); color: #fff; }

/* ── Check-in form card ── */
.form-card {
  background: var(--white);
  border: 1.5px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 28px 24px;
  box-shadow: var(--shadow);
  animation: card-in .35s cubic-bezier(.34,1.3,.64,1) both;
}
.field-label {
  display: block;
  font-size: .7rem;
  font-weight: 600;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: var(--accent);
  margin-bottom: 7px;
}
.field-input {
  width: 100%;
  background: white;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  padding: 13px 14px;
  font-size: 1rem;
  font-family: var(--font-body);
  color: var(--ink);
  transition: border-color .2s, background .2s;
  margin-bottom: 14px;
  -webkit-appearance: none;
  appearance: none;
}
.field-input::placeholder { color: var(--muted); opacity: .7; }
.field-input:focus-visible { outline: none; border-color: var(--accent); background: var(--white); box-shadow: 0 0 0 3px rgba(30,64,175,.1); }
.error-msg { font-size: .78rem; color: #991b1b; margin: -8px 0 12px; font-weight: 500; }
.submit-btn {
  width: 100%;
  padding: 15px;
  background: var(--accent);
  color: #fff;
  font-family: var(--font-head);
  font-size: 1rem;
  font-weight: 700;
  letter-spacing: .02em;
  border: none;
  border-radius: var(--radius);
  cursor: pointer;
  transition: background .18s, box-shadow .18s, transform .12s;
  box-shadow: 0 4px 18px rgba(30,64,175,.32);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  -webkit-tap-highlight-color: transparent;
}
.submit-btn:hover  { background: #1e3a8a; box-shadow: 0 6px 26px rgba(30,64,175,.44); }
.submit-btn:active { transform: scale(.98); }

/* ── Divider with text ── */
.or-divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 20px 0 4px;
  color: var(--muted);
  font-size: .75rem;
  font-weight: 500;
  letter-spacing: .05em;
}
.or-divider::before,
.or-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border);
}

/* ── Phone modal trigger link ── */
.phone-link {
  display: block;
  width: 100%;
  margin-top: 14px;
  padding: 12px;
  background: transparent;
  border: 1.5px dashed var(--border);
  border-radius: var(--radius-sm);
  color: var(--muted);
  font-family: var(--font-body);
  font-size: .85rem;
  font-weight: 500;
  cursor: pointer;
  transition: border-color .2s, color .2s, background .2s;
  text-align: center;
  letter-spacing: .01em;
}
.phone-link:hover {
  border-color: var(--accent-2);
  color: var(--accent);
  background: var(--accent-soft);
}
.phone-link svg { vertical-align: middle; margin-right: 6px; }

.checkin-hint {
  margin-top: 20px;
  font-size: .75rem;
  color: var(--muted);
  text-align: center;
  line-height: 1.6;
  padding: 0 8px;
}

.checkin-footer {
  background: var(--cream);
  border-top: 1.5px solid var(--border);
  padding: 16px 24px;
  text-align: center;
}
.checkin-footer p { font-size: .72rem; color: var(--muted); letter-spacing: .03em; }
.checkin-footer strong { color: var(--ink); }

/* ── Toast notification ── */
.toast {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%) translateY(-80px);
  background: #0f172a;
  color: #fff;
  padding: 12px 22px;
  border-radius: 10px;
  font-size: .84rem;
  font-weight: 500;
  z-index: 999;
  box-shadow: 0 8px 32px rgba(15,23,42,.25);
  transition: transform .35s cubic-bezier(.34,1.3,.64,1), opacity .3s;
  opacity: 0;
  white-space: nowrap;
  max-width: calc(100vw - 32px);
  text-align: center;
}
.toast.toast-success { background: #166534; }
.toast.toast-error   { background: #991b1b; }
.toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }

/* ── Shared Modal Base ── */
.modal-backdrop {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(15,23,42,.55);
  z-index: 200;
  align-items: flex-end;
  justify-content: center;
}
.modal-backdrop.open { display: flex; }
.modal-sheet {
  background: var(--white);
  border-radius: 24px 24px 0 0;
  width: 100%;
  max-width: 520px;
  max-height: 90vh;
  overflow-y: auto;
  padding: 20px 20px 40px;
  animation: sdin .32s cubic-bezier(.34,1.25,.64,1) both;
  -webkit-overflow-scrolling: touch;
}
@keyframes sdin  { from { transform:translateY(100%); } to { transform:translateY(0); } }
@keyframes sdout { from { transform:translateY(0); } to { transform:translateY(100%); } }
.modal-sheet.closing { animation: sdout .22s ease both; }
.modal-handle {
  width: 40px; height: 4px;
  background: var(--border);
  border-radius: 99px;
  margin: 0 auto 20px;
}
.modal-title {
  font-family: var(--font-head);
  font-size: 1.3rem;
  font-weight: 800;
  letter-spacing: -.02em;
  color: var(--ink);
  margin-bottom: 4px;
}
.modal-sub {
  font-size: .8rem;
  color: var(--muted);
  margin-bottom: 22px;
  line-height: 1.5;
}
.modal-divider { border-top: 1.5px solid var(--border); margin: 22px 0 18px; }
.modal-btn-row {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  align-items: center;
  flex-wrap: wrap;
}
.modal-btn-cancel {
  padding: 11px 22px;
  border: 1.5px solid #bfdbfe;
  border-radius: 8px;
  background: var(--white);
  color: var(--accent);
  font-family: var(--font-body);
  font-size: .86rem;
  font-weight: 500;
  cursor: pointer;
  transition: background .18s, border-color .18s;
  flex-shrink: 0;
}
.modal-btn-cancel:hover { background: #eff6ff; border-color: #93c5fd; }
.modal-btn-save {
  padding: 11px 28px;
  border: none;
  border-radius: 8px;
  background: var(--accent);
  color: var(--white);
  font-family: var(--font-head);
  font-size: .86rem;
  font-weight: 700;
  letter-spacing: .03em;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(30,64,175,.28);
  transition: background .18s, box-shadow .18s, transform .1s;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}
.modal-btn-save:hover  { background: #1e3a8a; box-shadow: 0 6px 20px rgba(30,64,175,.38); }
.modal-btn-save:active { transform: scale(.98); }
.modal-btn-save:disabled { opacity: .65; cursor: not-allowed; }
.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin .7s linear infinite;
  display: none;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Reg grid ── */
.reg-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
.reg-field { display: flex; flex-direction: column; gap: 5px; }
.reg-field.full { grid-column: 1 / -1; }
.reg-label {
  font-size: .68rem;
  font-weight: 600;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: var(--accent);
}
.reg-label .req { color: #ef4444; }
.reg-input {
  width: 100%;
  border: 1.5px solid #bfdbfe;
  border-radius: 8px;
  padding: 12px 14px;
  font-size: .92rem;
  background: #f8faff;
  color: var(--ink);
  font-family: var(--font-body);
  outline: none;
  transition: border-color .2s, box-shadow .2s;
  -webkit-appearance: none;
  appearance: none;
}
.reg-input:focus { border-color: var(--accent-2); box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
.reg-input.is-invalid { border-color: #fca5a5; background: #fff5f5; }
.reg-error { font-size: .72rem; color: #991b1b; font-weight: 500; margin-top: 2px; }

/* ── Phone modal ── */
.phone-modal-field { margin-bottom: 4px; }

/* ── Responsive ── */
@media (min-width: 768px) {
  .checkin-topbar { padding: 16px 40px; }
  .topbar-title { font-size: 1.1rem; }
  .checkin-hero { padding: 0 40px 0; }
  .hero-inner { padding: 56px 0 60px; }
  .checkin-main { padding: 40px 24px 64px; }
  .form-card { padding: 36px 32px; }
  .state-card { padding: 44px 36px; }
  .modal-sheet { padding: 28px 32px 48px; }
  .modal-backdrop { align-items: center; padding: 24px; }
  .modal-sheet { border-radius: 20px; max-height: 85vh; }
}
@media (min-width: 1024px) {
  .checkin-topbar { padding: 18px 60px; }
  .checkin-hero { padding: 0 60px 0; }
  .checkin-main { padding: 48px 24px 80px; }
  .hero-title { font-size: 3rem; }
  .hero-sub { font-size: .95rem; max-width: 340px; }
}
@media (max-width: 360px) {
  .checkin-topbar { padding: 12px 16px; }
  .topbar-title { font-size: .88rem; }
  .topbar-date { font-size: .6rem; padding: 3px 8px; }
  .checkin-hero { padding: 0 16px 0; }
  .hero-inner { padding: 28px 0 32px; }
  .hero-title { font-size: 1.65rem; }
  .hero-sub { font-size: .82rem; }
  .checkin-main { padding: 20px 12px 40px; }
  .form-card { padding: 22px 16px; }
  .state-card { padding: 28px 16px; }
  .state-icon-wrap { width: 76px; height: 76px; }
  .state-heading { font-size: 1.2rem; }
  .modal-btn-row { flex-direction: column-reverse; }
  .modal-btn-cancel, .modal-btn-save { width: 100%; justify-content: center; }
  .reg-grid { grid-template-columns: 1fr; }
  .not-found-actions .state-btn { width: 100%; text-align: center; }
  .phone-alt-btn { width: 100%; }
}
@media (hover: none) and (pointer: coarse) {
  .submit-btn { padding: 16px; }
  .state-btn, .btn-outline-terracotta { padding: 13px 32px; }
  .field-input, .reg-input { padding: 14px; }
  .modal-btn-cancel, .modal-btn-save { padding: 13px 22px; }
}
</style>
@endpush

@section('content')
<div class="checkin-page">

  {{-- ═══ TOP BAR ═══ --}}
  <header class="checkin-topbar">
    <span class="topbar-title">Sunday Service</span>
    <span class="topbar-date">{{ now()->format('D, d M Y') }}</span>
  </header>

  {{-- ═══ HERO ═══ --}}
  <section class="checkin-hero" aria-label="Service check-in welcome">
    <div class="hero-glow-bottom" aria-hidden="true"></div>
    <div class="hero-inner">
      <span class="hero-live-badge" aria-label="Service is live">Live Now</span>
      <h1 class="hero-title">Welcome to<br><em>Church</em></h1>
      <p class="hero-sub">Check in below to record your attendance for today's Sunday service.</p>
    </div>
    <div class="hero-wave" aria-hidden="true">
      <svg viewBox="0 0 1440 48" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0 48 C360 0 1080 0 1440 48 L1440 48 L0 48 Z" fill="#f8fafc"/>
      </svg>
    </div>
  </section>

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
          @if(session('children_count') > 0)
            <p class="state-body">
              + {{ session('children_count') }} {{ Str::plural('child', session('children_count')) }} checked in with you.
            </p>
          @endif
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
            We couldn't find <strong style="color:var(--ink);">{{ session('attempted_email') }}</strong> in our records.
          </p>

          <div class="not-found-actions">

            {{-- Try again --}}
            <a href="{{ route('checkin') }}" class="state-btn">Try Again →</a>

            <div class="action-divider">or check in another way</div>

            {{-- Phone number fallback --}}
            <button type="button" class="phone-alt-btn" onclick="openPhoneModal()">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              Use my phone number instead
            </button>

            <div class="action-divider">not registered yet?</div>

            {{-- Register --}}
            <button type="button" class="register-link" onclick="openRegModal()">
              Register as a new member →
            </button>

          </div>
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
          <h2 class="state-heading">You're All Set!</h2>
          <p class="state-body">
            Your details have been received and your profile is now active.<br>
            You can check in below right away. God bless you.
          </p>
          <a href="{{ route('checkin') }}" class="state-btn">Back to Check-in →</a>
        </div>

      @else
        {{-- ── PRIMARY FORM: Email check-in ── --}}
        <div class="form-card">
          <form method="POST" action="{{ route('checkin.store') }}">
            @csrf
            <label for="email" class="field-label">Your Email Address</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="e.g. john.doe@example.com"
              class="field-input"
              value="{{ old('email') }}"
              required
              autocomplete="email"
              aria-required="true"
              inputmode="email"
            />
            @error('email')
              <p class="error-msg" role="alert">{{ $message }}</p>
            @enderror

            <label for="children_count" class="field-label">Children With You</label>
            <input
              type="number"
              id="children_count"
              name="children_count"
              min="0"
              max="20"
              step="1"
              placeholder="0"
              class="field-input"
              value="{{ old('children_count', 0) }}"
              inputmode="numeric"
            />
            @error('children_count')
              <p class="error-msg" role="alert">{{ $message }}</p>
            @enderror

            <button type="submit" class="submit-btn">
              Mark My Attendance <span aria-hidden="true">→</span>
            </button>
          </form>

          <div class="or-divider">or</div>

          <button type="button" class="phone-link" onclick="openPhoneModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.8a16 16 0 0 0 6.29 6.29l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            Check in with phone number instead
          </button>
        </div>
      @endif

      <p class="checkin-hint">
        If your details aren't recognised, you can register below or speak with an administrator.
      </p>
    </div>
  </main>

  <footer class="checkin-footer">
    <p>© {{ now()->year }} <strong>Sunday Service</strong> · Attendance System</p>
  </footer>
</div>

{{-- ═══ TOAST ═══ --}}
<div class="toast" id="toast" role="status" aria-live="polite"></div>

{{-- ═══ PHONE CHECK-IN MODAL ═══ --}}
<div class="modal-backdrop" id="phoneModalBackdrop" role="dialog" aria-modal="true" aria-labelledby="phoneModalTitle">
  <div class="modal-sheet" id="phoneModalSheet">
    <div class="modal-handle" aria-hidden="true"></div>
    <h2 class="modal-title" id="phoneModalTitle">Check In with Phone</h2>
    <p class="modal-sub">Enter the phone number linked to your member profile.</p>

    <form method="POST" action="{{ route('checkin.store') }}" id="phoneForm">
      @csrf
      <div class="phone-modal-field">
        <label class="reg-label" for="phone_modal">Phone Number <span class="req" style="color:#ef4444">*</span></label>
        <input
          type="tel"
          id="phone_modal"
          name="phone"
          placeholder="e.g. 08012345678"
          class="reg-input"
          value="{{ old('phone') }}"
          required
          autocomplete="tel"
          inputmode="tel"
          style="margin-top: 6px;"
        />
        @error('phone')
          <span class="reg-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="phone-modal-field" style="margin-top: 14px;">
        <label class="reg-label" for="children_modal">Children With You</label>
        <input
          type="number"
          id="children_modal"
          name="children_count"
          min="0"
          max="20"
          step="1"
          placeholder="0"
          class="reg-input"
          value="{{ old('children_count', 0) }}"
          inputmode="numeric"
          style="margin-top: 6px;"
        />
        @error('children_count')
          <span class="reg-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="modal-divider"></div>

      <div class="modal-btn-row">
        <button type="button" class="modal-btn-cancel" onclick="closePhoneModal()">Cancel</button>
        <button type="submit" class="modal-btn-save" id="phoneSubmitBtn">
          <span id="phoneBtnText">Mark Attendance</span>
          <span class="spinner" id="phoneSpinner"></span>
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ═══ REGISTRATION MODAL ═══ --}}
<div class="modal-backdrop" id="regModalBackdrop" role="dialog" aria-modal="true" aria-labelledby="regModalTitle">
  <div class="modal-sheet" id="regModalSheet">
    <div class="modal-handle" aria-hidden="true"></div>

    <h2 class="modal-title" id="regModalTitle">New Member Registration</h2>
    <p class="modal-sub">Fill in your details and tap Register — you'll be able to check in immediately after.</p>

    <form method="POST" action="{{ route('members.store') }}" id="regForm" novalidate>
      @csrf

      <div class="reg-grid">

        <div class="reg-field">
          <label class="reg-label" for="reg_first_name">First Name <span class="req">*</span></label>
          <input type="text" name="first_name" id="reg_first_name" required
            value="{{ old('first_name') }}" placeholder="e.g. John"
            class="reg-input @error('first_name') is-invalid @enderror"
            autocomplete="given-name">
          @error('first_name')
            <span class="reg-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="reg-field">
          <label class="reg-label" for="reg_last_name">Last Name <span class="req">*</span></label>
          <input type="text" name="last_name" id="reg_last_name" required
            value="{{ old('last_name') }}" placeholder="e.g. Doe"
            class="reg-input @error('last_name') is-invalid @enderror"
            autocomplete="family-name">
          @error('last_name')
            <span class="reg-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="reg-field full">
          <label class="reg-label" for="reg_email">Email Address <span class="req">*</span></label>
          <input type="email" name="email" id="reg_email" required
            value="{{ old('email', session('attempted_email')) }}"
            placeholder="e.g. john@example.com"
            class="reg-input @error('email') is-invalid @enderror"
            autocomplete="email"
            inputmode="email">
          @error('email')
            <span class="reg-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="reg-field full">
          <label class="reg-label" for="reg_phone">
            Phone Number
            <span style="color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span>
          </label>
          <input type="tel" name="phone" id="reg_phone"
            value="{{ old('phone', session('attempted_phone')) }}"
            placeholder="e.g. 08012345678"
            class="reg-input @error('phone') is-invalid @enderror"
            autocomplete="tel"
            inputmode="tel">
          @error('phone')
            <span class="reg-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="reg-field full">
          <label class="reg-label" for="reg_church">Church/Fellowship</label>
          <input type="text" name="church" id="reg_church"
            value="{{ old('church') }}" placeholder="e.g. CE Lekki"
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
/* ── Phone modal ── */
const phoneBackdrop = document.getElementById('phoneModalBackdrop');
const phoneSheet    = document.getElementById('phoneModalSheet');

function openPhoneModal() {
  phoneBackdrop.classList.add('open');
  phoneSheet.classList.remove('closing');
  document.body.style.overflow = 'hidden';
  setTimeout(() => {
    const inp = document.getElementById('phone_modal');
    if (inp) inp.focus();
  }, 350);
}
function closePhoneModal() {
  phoneSheet.classList.add('closing');
  setTimeout(() => {
    phoneBackdrop.classList.remove('open');
    phoneSheet.classList.remove('closing');
    document.body.style.overflow = '';
  }, 230);
}
phoneBackdrop.addEventListener('click', e => { if (e.target === phoneBackdrop) closePhoneModal(); });

document.getElementById('phoneForm').addEventListener('submit', function() {
  const btn     = document.getElementById('phoneSubmitBtn');
  const spinner = document.getElementById('phoneSpinner');
  const text    = document.getElementById('phoneBtnText');
  btn.disabled  = true;
  spinner.style.display = 'block';
  text.textContent = 'Checking in…';
});

/* ── Registration modal ── */
const regBackdrop = document.getElementById('regModalBackdrop');
const regSheet    = document.getElementById('regModalSheet');

function openRegModal() {
  closePhoneModal();
  setTimeout(() => {
    regBackdrop.classList.add('open');
    regSheet.classList.remove('closing');
    document.body.style.overflow = 'hidden';
    setTimeout(() => {
      const first = regSheet.querySelector('input:not([type=hidden])[required]');
      if (first) first.focus();
    }, 350);
  }, 240);
}
function closeRegModal() {
  regSheet.classList.add('closing');
  setTimeout(() => {
    regBackdrop.classList.remove('open');
    regSheet.classList.remove('closing');
    document.body.style.overflow = '';
  }, 230);
}
regBackdrop.addEventListener('click', e => { if (e.target === regBackdrop) closeRegModal(); });

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    if (regBackdrop.classList.contains('open'))   closeRegModal();
    if (phoneBackdrop.classList.contains('open')) closePhoneModal();
  }
});

document.getElementById('regForm').addEventListener('submit', function() {
  const btn     = document.getElementById('regSubmitBtn');
  const spinner = document.getElementById('regSpinner');
  const text    = document.getElementById('regBtnText');
  btn.disabled  = true;
  spinner.style.display = 'block';
  text.textContent = 'Registering…';
});

@if($errors->any())
  document.addEventListener('DOMContentLoaded', openRegModal);
@endif

/* ── Toast ── */
function showToast(message, type = 'success') {
  const toast = document.getElementById('toast');
  toast.textContent = message;
  toast.className   = 'toast toast-' + type + ' show';
  setTimeout(() => { toast.classList.remove('show'); }, 4000);
}

@if(session('status') === 'registered')
  document.addEventListener('DOMContentLoaded', () => showToast('Registered and activated — you can check in now!', 'success'));
@elseif(session('status') === 'success')
  document.addEventListener('DOMContentLoaded', () => showToast('Attendance marked! God bless you.', 'success'));
@elseif(session('status') === 'duplicate')
  document.addEventListener('DOMContentLoaded', () => showToast('Already checked in today.', 'error'));
@elseif(session('status') === 'pending_activation')
  document.addEventListener('DOMContentLoaded', () => showToast('Profile review pending approval.', 'error'));
@elseif(session('status') === 'error')
  document.addEventListener('DOMContentLoaded', () => showToast('Something went wrong. Please try again.', 'error'));
@endif

/* ── Checked-in counter animation ── */
document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('checkin-count');
  if (!el) return;
  const target = {{ session('checkin_count') ?? 0 }};
  if (target === 0) { el.textContent = '0'; return; }
  let current = 0;
  const step  = Math.ceil(target / 40);
  const timer = setInterval(() => {
    current = Math.min(current + step, target);
    el.textContent = current;
    if (current >= target) clearInterval(timer);
  }, 30);
});
</script>
@endpush
@endsection
