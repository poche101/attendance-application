@extends('layouts.app')
@section('title', 'Sunday Service Check-in')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
<style>
/* ── Design tokens ─────────────────── */
:root {
  --ink:        #0f172a;
  --paper:      #f8fafc;
  --cream:      #f1f5f9;
  --border:     #e2e8f0;
  --muted:      #64748b;
  --white:      #ffffff;
  --accent:     #1e40af;     /* Main Blue */
  --accent-2:   #3b82f6;     /* Light Blue */
  --accent-soft:#dbeafe;
  --radius:     14px;
  --radius-sm:  8px;
  --radius-lg:  22px;
  --shadow:     0 4px 20px rgba(15,23,42,.08);
  --shadow-lg:  0 10px 48px rgba(15,23,42,.12);
  --font-head:  'Syne', sans-serif;
  --font-body:  'DM Sans', sans-serif;
  --max-w:      440px;
}

/* ── Reset ─────────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: var(--font-body);
  background: var(--paper);
  color: var(--ink);
  min-height: 100vh;
  overflow-x: hidden;
}
a { color: inherit; text-decoration: none; }
button, input { font-family: var(--font-body); }
input:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
button:focus-visible { outline: 2px solid var(--accent); outline-offset: 3px; }

/* ── Page wrapper ───────────────────────────────────────────────── */
.checkin-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* ── Hero bar ──────────────────────── */
.checkin-hero {
  position: relative;
  background: var(--ink);
  color: white;
  overflow: hidden;
  padding: 0 24px;
}

/* Grain overlay */
.checkin-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E");
  pointer-events: none;
  z-index: 0;
}

/* Accent radial blobs */
.checkin-hero::after {
  content: '';
  position: absolute;
  width: 300px; height: 300px;
  background: radial-gradient(circle, var(--accent) 0%, transparent 70%);
  top: -100px; right: -80px;
  opacity: .16;
  pointer-events: none;
  z-index: 0;
}
.hero-blob {
  position: absolute;
  width: 220px; height: 220px;
  background: radial-gradient(circle, var(--accent-2) 0%, transparent 70%);
  bottom: -70px; left: -50px;
  opacity: .13;
  pointer-events: none;
  z-index: 0;
}

/* Top nav strip inside hero */
.hero-nav {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 0 16px;
  border-bottom: 1px solid rgba(255,255,255,.1);
  max-width: var(--max-w);
  margin: 0 auto;
  width: 100%;
}
.hero-logo {
  font-family: var(--font-head);
  font-size: 1.4rem;
  font-weight: 800;
  letter-spacing: -.02em;
}
.hero-logo span { color: var(--accent-2); }

.hero-date-badge {
  font-size: .68rem;
  font-weight: 600;
  letter-spacing: .08em;
  text-transform: uppercase;
  background: rgba(255,255,255,.1);
  border: 1px solid rgba(255,255,255,.2);
  color: var(--accent-2);
  padding: 5px 11px;
  border-radius: 99px;
}

/* Hero body */
.hero-body {
  position: relative;
  z-index: 1;
  max-width: var(--max-w);
  margin: 0 auto;
  padding: 36px 0 40px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 0;
}

.hero-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: .7rem;
  font-weight: 600;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--accent-2);
  margin-bottom: 14px;
}
.hero-label::before {
  content: '';
  display: inline-block;
  width: 6px; height: 6px;
  background: var(--accent-2);
  border-radius: 50%;
  animation: pulse-dot 2s infinite;
}
@keyframes pulse-dot {
  0%,100% { opacity:1; transform:scale(1); }
  50%      { opacity:.35; transform:scale(1.5); }
}

.hero-title {
  font-family: var(--font-head);
  font-size: clamp(2rem, 9vw, 2.7rem);
  font-weight: 800;
  line-height: 1.08;
  letter-spacing: -.03em;
  color: white;
  margin-bottom: 12px;
}
.hero-title em {
  font-style: italic;
  color: var(--accent-2);
}

.hero-sub {
  font-size: .9rem;
  line-height: 1.6;
  color: rgba(255,255,255,.75);
  max-width: 320px;
  margin-bottom: 28px;
}

/* QR decorative tile */
.qr-tile {
  width: 148px; height: 148px;
  border-radius: var(--radius);
  background: rgba(219,234,254,.1);
  border: 1.5px dashed rgba(59,130,246,.4);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 0;
}
.qr-grid {
  display: grid;
  gap: 2px;
  grid-template-columns: repeat(7, 11px);
}
.qr-cell {
  width: 11px; height: 11px;
  border-radius: 2px;
}
.qr-caption {
  font-size: .6rem;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: rgba(59,130,246,.7);
  font-family: var(--font-body);
}

/* ── Main content area ──────────────────────────────────────────── */
.checkin-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 32px 24px 48px;
}

.checkin-card-wrap {
  width: 100%;
  max-width: var(--max-w);
}

/* ── Cards ─────────────────────────────── */
.state-card {
  background: var(--white);
  border: 1.5px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 36px 28px;
  text-align: center;
  box-shadow: var(--shadow);
  animation: card-in .35s cubic-bezier(.34,1.3,.64,1) both;
}
@keyframes card-in {
  from { opacity:0; transform:translateY(14px) scale(.98); }
  to   { opacity:1; transform:translateY(0) scale(1); }
}

/* Icon circle */
.state-icon-wrap {
  width: 100px; height: 100px;
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
  font-size: 1.55rem;
  font-weight: 800;
  letter-spacing: -.025em;
  color: var(--ink);
  line-height: 1.15;
  margin-bottom: 8px;
}
.state-member {
  font-size: .88rem;
  font-weight: 600;
  color: var(--accent);
  margin-bottom: 4px;
}
.state-body {
  font-size: .85rem;
  line-height: 1.55;
  color: var(--muted);
  margin-top: 6px;
}
.state-body.error-text { color: #991b1b; font-weight: 500; margin-bottom: 16px; }

/* CTA button */
.state-btn {
  display: inline-block;
  margin-top: 22px;
  padding: 11px 30px;
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

/* Outline variant */
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

/* ── Default form card ──────────────────────────────────────────── */
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
  padding: 12px 14px;
  font-size: .92rem;
  font-family: var(--font-body);
  color: var(--ink);
  transition: border-color .2s, background .2s;
  margin-bottom: 14px;
}
.field-input::placeholder { color: var(--muted); opacity: .7; }
.field-input:focus-visible {
  outline: none;
  border-color: var(--accent);
  background: var(--white);
}

.error-msg {
  font-size: .78rem;
  color: #991b1b;
  margin: -8px 0 12px;
  font-weight: 500;
}

.submit-btn {
  width: 100%;
  padding: 14px;
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
}
.submit-btn:hover  { background: #1e3a8a; box-shadow: 0 6px 26px rgba(30,64,175,.44); }
.submit-btn:active { transform: scale(.98); }

/* ── Footer hint ────────────────────────────────────────────────── */
.checkin-hint {
  margin-top: 20px;
  font-size: .75rem;
  color: var(--muted);
  text-align: center;
  line-height: 1.5;
}

/* ── Footer bar ─────────────────────────────────────────────────── */
.checkin-footer {
  background: var(--cream);
  border-top: 1.5px solid var(--border);
  padding: 16px 24px;
  text-align: center;
}
.checkin-footer p {
  font-size: .72rem;
  color: var(--muted);
  letter-spacing: .03em;
}
.checkin-footer strong { color: var(--ink); }
</style>
@endpush

@section('content')
<div class="checkin-page">

  {{-- ═══════════ HERO ═══════════ --}}
  <header class="checkin-hero">
    <div class="hero-blob" aria-hidden="true"></div>

    <nav class="hero-nav">
      <span class="hero-date-badge">{{ now()->format('D, d M Y') }}</span>
    </nav>

    <div class="hero-body">
      <p class="hero-label" aria-hidden="true">Sunday Service · Check-in</p>
      <h1 class="hero-title">
        Welcome to<br><em>Church</em>
      </h1>
      <p class="hero-sub">
        Mark your attendance by entering your email address below
      </p>

      {{-- QR code visual --}}
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

  {{-- ═══════════ MAIN ═══════════ --}}
  <main class="checkin-main">
    <div class="checkin-card-wrap">

      {{-- ── Error state ── --}}
      @if(session('status') === 'error')
        <div class="state-card" role="alert">
          <p class="state-body error-text">
            Something went wrong saving your attendance. Please try again or contact an administrator.
          </p>
          <a href="{{ route('checkin') }}" class="btn-outline-terracotta">Try Again</a>
        </div>

      {{-- ── Success state ── --}}
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

      {{-- ── Duplicate state ── --}}
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
          <p class="state-body">You're already marked present for today's service.</p>

          <a href="{{ route('checkin') }}" class="state-btn">Mark Another →</a>
        </div>

      {{-- ── Not found state ── --}}
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
          <p class="state-body">Please speak with an administrator to get registered.</p>

          <a href="{{ route('checkin') }}" class="state-btn">Try Again →</a>
        </div>

      {{-- ── Default form ── --}}
      @else
        <div class="form-card">
          <form method="POST" action="{{ route('checkin.store') }}">
            @csrf

            <label for="email" class="field-label">Your Email Address</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="yourname@example.com"
              class="field-input"
              value="{{ old('email') }}"
              required
              autocomplete="email"
              aria-required="true"
            />
            @error('email')
              <p class="error-msg" role="alert">{{ $message }}</p>
            @enderror

            <button type="submit" class="submit-btn">
              Mark My Attendance
              <span aria-hidden="true">→</span>
            </button>
          </form>
        </div>
      @endif

      <p class="checkin-hint">
        If your email isn't recognised, please speak with an administrator.
      </p>
    </div>
  </main>

  {{-- ═══════════ FOOTER ═══════════ --}}
  <footer class="checkin-footer">
    <p>© {{ now()->year }} <strong>Sunday Service</strong> · Attendance System</p>
  </footer>

</div>
@endsection
