<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Grace Attendance')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                        sans:  ['Jost', 'sans-serif'],
                    },
                    colors: {
                        or: {
                            50:  '#FFFBF5',
                            100: '#FEE9CF',
                            200: '#FAD9B5',
                            300: '#F0A055',
                            400: '#C45E08',
                            500: '#A34C06',
                            600: '#7A3E08',
                            700: '#B86A1A',
                            800: '#1E1208',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Jost', sans-serif; background: #FFFBF5; }
        .cg  { font-family: 'Cormorant Garamond', Georgia, serif; }
        input, select { font-family: 'Jost', sans-serif; }
        input:focus, select:focus { outline: none; border-color: #B86A1A !important; box-shadow: 0 0 0 2px rgba(184,106,26,0.15); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #FEE9CF; }
        ::-webkit-scrollbar-thumb { background: #F0A055; border-radius: 3px; }
        @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        @keyframes pop   { 0%{transform:scale(0.8);opacity:0;} 70%{transform:scale(1.05);} 100%{transform:scale(1);opacity:1;} }
        .anim-fadeup { animation: fadeUp 0.3s ease; }
        .anim-pop    { animation: pop 0.4s cubic-bezier(0.36,0.07,0.19,0.97); }
        .form-input  { width:100%; border:1px solid #FAD9B5; border-radius:8px; padding:10px 14px; font-size:14px; background:#FFFBF5; color:#1E1208; transition:border 0.2s; }
        .form-label  { font-size:12px; letter-spacing:0.07em; text-transform:uppercase; color:#B86A1A; margin-bottom:6px; display:block; }
        .btn-primary { background:#C45E08; color:#fff; border:none; border-radius:6px; padding:10px 24px; font-family:'Jost',sans-serif; font-size:14px; letter-spacing:0.06em; cursor:pointer; transition:all 0.2s; display:inline-block; }
        .btn-primary:hover { background:#A34C06; transform:translateY(-1px); }
        .btn-outline { background:transparent; color:#C45E08; border:1px solid #F0A055; border-radius:6px; padding:8px 18px; font-family:'Jost',sans-serif; font-size:13px; letter-spacing:0.04em; cursor:pointer; transition:all 0.2s; display:inline-block; }
        .btn-outline:hover { background:rgba(196,94,8,0.06); }
        .btn-sm { padding:5px 12px; font-size:12px; }
        .table-row:hover { background:#FFF8EE; }
        .badge { display:inline-block; padding:2px 10px; border-radius:20px; font-size:11px; letter-spacing:0.04em; font-weight:500; }
        .rank-bar-bg   { background:#FEE9CF; border-radius:4px; height:6px; flex:1; }
        .rank-bar-fill { background:linear-gradient(90deg,#F0A055,#B86A1A); border-radius:4px; height:6px; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">

{{-- Nav --}}
<nav class="bg-white border-b border-or-200 sticky top-0 z-50" style="border-color:#FAD9B5;">
    <div class="px-8 h-[60px] flex items-center gap-2">
        <div class="flex items-center gap-2.5 flex-1">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm" style="background:#C45E08;">✦</div>
            <span class="cg text-xl font-semibold" style="color:#1E1208;">Service Attendance Dashboard</span>
        </div>
        <div class="flex gap-1">
            <a href="{{ route('checkin') }}" class="px-4 py-2 rounded text-xs tracking-widest uppercase transition-all" style="color:#7A3E08;">Check In</a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded text-xs tracking-widest uppercase transition-all {{ request()->routeIs('admin.*') ? 'font-medium' : '' }}" style="color:#C45E08; {{ request()->routeIs('admin.*') ? 'background:#FEE9CF;' : '' }}">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button class="px-4 py-2 rounded text-xs tracking-widest uppercase transition-all hover:bg-red-50" style="color:#991b1b;">Sign out</button>
            </form>
        </div>
    </div>
</nav>

{{-- Toast --}}
@if(session('toast'))
<div class="fixed bottom-8 right-8 z-[200] px-5 py-3.5 rounded-xl text-sm shadow-lg anim-fadeup max-w-xs" style="background:#d1fae5; color:#065f46;">
    {{ session('toast') }}
</div>
@endif

<div class="flex" style="min-height: calc(100vh - 61px);">
    {{-- Sidebar --}}
    <aside class="w-[220px] bg-white border-r flex-shrink-0 p-7" style="border-color:#FAD9B5;">
        @php
            $navItems = [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard',    'icon' => '⊞'],
                ['route' => 'admin.members',   'label' => 'Members',      'icon' => '⊙'],
                ['route' => 'admin.rankings',  'label' => 'Top Rankings', 'icon' => '◈'],
                ['route' => 'admin.export',    'label' => 'Export',       'icon' => '↓'],
            ];
        @endphp
        @foreach($navItems as $item)
        <a href="{{ route($item['route']) }}"
           class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-lg mb-1 text-sm transition-all no-underline"
           style="{{ request()->routeIs($item['route']) ? 'background:#FEE9CF; color:#C45E08; font-weight:500;' : 'color:#7A3E08;' }}">
            <span class="text-base">{{ $item['icon'] }}</span> {{ $item['label'] }}
        </a>
        @endforeach
    </aside>

    {{-- Main --}}
    <main class="flex-1 p-8 overflow-x-hidden">
        @yield('main')
    </main>
</div>

{{-- Footer --}}
<footer class="border-t py-4 text-center" style="border-color:#FAD9B5;">
    <span class="text-xs tracking-wide" style="color:#F0A055;">Grace Attendance System · v1.1 · {{ date('Y') }}</span>
</footer>

@stack('scripts')
</body>
</html>
