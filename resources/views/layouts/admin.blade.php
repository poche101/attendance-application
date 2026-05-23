<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Grace Attendance')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            content: [],
            theme: {
                extend: {
                    fontFamily: {
                        head: ['Syne', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif'],
                    },
                    colors: {
                        accent: {
                            600: '#1E40AF',
                            500: '#3B82F6',
                            100: '#DBEAFE',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --ink: #0d0d0d;
            --paper: #f8fafc;
            --accent: #1E40AF;
            --accent-light: #3B82F6;
            --accent-soft: #DBEAFE;
            --border: #e2e8f0;
            --muted: #64748b;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--paper);
        }

        .cg, .font-head {
            font-family: 'Syne', sans-serif;
        }

        .admin-nav {
            background: white;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-link {
            transition: all 0.2s;
        }

        .sidebar-link.active {
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 600;
        }

        .form-input {
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
        }

        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        .btn-primary {
            background: var(--accent);
            color: white;
            font-weight: 600;
            letter-spacing: 0.025em;
        }

        .btn-outline {
            border: 1.5px solid var(--border);
            color: var(--ink);
        }

        .badge-blue {
            background: var(--accent-soft);
            color: var(--accent);
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50">

{{-- Top Navigation --}}
<nav class="admin-nav shadow-sm sticky top-0 z-50">
    <div class="px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-white-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                <img src="/images/lekki-logo.png" alt="logo">
            </div>
            <div>
                <span class="font-head text-2xl tracking-tight text-slate-900">Sunday Service Attendance</span>
                <span class="text-blue-600 font-semibold">Dashboard</span>
            </div>
        </div>

        <div class="flex items-center gap-6 text-sm">
            <a href="{{ route('checkin') }}"
               class="px-5 py-2 rounded-xl hover:bg-slate-100 transition-colors">
                Check In
            </a>
            <a href="{{ route('admin.dashboard') }}"
               class="px-5 py-2 rounded-xl {{ request()->routeIs('admin.*') ? 'bg-blue-50 text-blue-700 font-medium' : 'hover:bg-slate-100' }} transition-colors">
                Dashboard
            </a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                        class="px-5 py-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                    Sign out
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- Toast Notification --}}
@if(session('toast'))
    <div class="fixed bottom-6 right-6 z-[200] px-6 py-4 rounded-2xl text-sm shadow-xl flex items-center gap-3 bg-emerald-100 text-emerald-800 border border-emerald-200">
        {{ session('toast') }}
    </div>
@endif

<div class="flex" style="min-height: calc(100vh - 64px);">

    {{-- Sidebar --}}
    <aside class="w-56 bg-white border-r border-slate-200 flex-shrink-0 py-8 px-5">
        @php
            $navItems = [
                ['route' => 'admin.dashboard',      'label' => 'Dashboard',    'icon' => '⊞'],
                ['route' => 'admin.members.index',  'label' => 'Members',      'icon' => '👥'],
                ['route' => 'admin.rankings',       'label' => 'Top Rankings', 'icon' => '🏆'],
                ['route' => 'admin.export',     'label' => 'Export Data',  'icon' => '↓'],
            ];
        @endphp

        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl mb-1 text-slate-700 {{ request()->routeIs($item['route'] . '*') ? 'active' : '' }}">
                <span class="text-lg">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </aside>

    {{-- Main Content Area --}}
    <main class="flex-1 p-8 overflow-auto">
        @yield('main')
    </main>
</div>

{{-- Footer --}}
<footer class="bg-white border-t border-slate-200 py-5 text-center text-xs text-slate-500">
    Christ Embassy Lekki Attendance System © {{ date('Y') }} • All Rights Reserved
</footer>

@stack('scripts')
</body>
</html>
