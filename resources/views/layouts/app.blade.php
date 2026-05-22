<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Grace Attendance')</title>
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
                        orange: {
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
        .cg { font-family: 'Cormorant Garamond', Georgia, serif; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #B86A1A !important; box-shadow: 0 0 0 2px rgba(184,106,26,0.15); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #FEE9CF; }
        ::-webkit-scrollbar-thumb { background: #F0A055; border-radius: 3px; }
        @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        @keyframes pop   { 0% { transform:scale(0.8); opacity:0; } 70% { transform:scale(1.05); } 100% { transform:scale(1); opacity:1; } }
        .anim-fadeup { animation: fadeUp 0.3s ease; }
        .anim-pop    { animation: pop 0.4s cubic-bezier(0.36,0.07,0.19,0.97); }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">

{{-- Nav --}}
<nav class="bg-white border-b border-orange-200 sticky top-0 z-50">
    <div class="px-8 h-[60px] flex items-center gap-2">
        <div class="flex items-center gap-2.5 flex-1">
            <img src="/images/lekki-logo.png" alt="logo" style="width:80px;">
            <span class="cg text-xl font-semibold text-orange-800 tracking-wide">Christ Embassy Lekki</span>
        </div>
        <div class="flex gap-1">
            <a href="{{ route('checkin') }}" class="px-4 py-2 rounded text-xs tracking-widest uppercase transition-all text-orange-600 hover:bg-orange-100 {{ request()->routeIs('checkin') ? 'bg-orange-100 text-orange-400' : '' }}">Check In</a>
        </div>
    </div>
</nav>

{{-- Toast --}}
@if(session('toast'))
<div class="fixed bottom-8 right-8 z-[200] px-5 py-3.5 rounded-xl text-sm bg-green-100 text-green-800 shadow-lg anim-fadeup max-w-xs">
    {{ session('toast') }}
</div>
@endif

@yield('content')

{{-- Footer --}}
<footer class="border-t border-orange-200 py-4 text-center">
    <span class="text-xs text-orange-300 tracking-wide">Christ Embassy Lekki Attendance System · v1.1 · {{ date('Y') }}</span>
</footer>

@stack('scripts')
</body>
</html>
