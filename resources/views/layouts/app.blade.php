<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Grace Attendance')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        head: ['Syne', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                    colors: {
                        accent: {
                            50:  '#EFF6FF',
                            100: '#DBEAFE',
                            500: '#3B82F6',
                            600: '#1E40AF',   // Primary Blue
                            700: '#1E3A8A',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --ink: #0f172a;
            --paper: #f8fafc;
            --accent: #1e40af;
            --accent-light: #3b82f6;
            --accent-soft: #dbeafe;
            --border: #e2e8f0;
            --muted: #64748b;
        }

        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: var(--paper);
        }

        .font-head {
            font-family: 'Syne', sans-serif;
        }

        .nav-link {
            transition: all 0.2s ease;
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50">

{{-- Top Navigation --}}
<nav class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-50">
    <div class="px-8 h-16 flex items-center gap-2">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-9 h-9 bg-white-600 rounded-2xl flex items-center justify-center text-white font-bold text-xl">
               <img src="/images/lekki-logo.png" alt="logo">
            </div>
            <span class="font-head text-2xl tracking-tight text-slate-900">
                Christ Embassk Lekki <span class="text-blue-600">Attendance</span>
            </span>
        </div>

        <div class="flex gap-1">
            <a href="{{ route('checkin') }}"
               class="nav-link px-5 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('checkin') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                Check In
            </a>
        </div>
    </div>
</nav>

{{-- Toast --}}
@if(session('toast'))
<div class="fixed bottom-8 right-8 z-[200] px-6 py-4 rounded-2xl text-sm shadow-xl flex items-center gap-3 bg-emerald-100 text-emerald-800 border border-emerald-200">
    {{ session('toast') }}
</div>
@endif

@yield('content')

{{-- Footer --}}
<footer class="bg-white border-t border-slate-200 py-5 text-center text-xs text-slate-500">
    Grace Attendance System © {{ date('Y') }} • All Rights Reserved
</footer>

@stack('scripts')
</body>
</html>
