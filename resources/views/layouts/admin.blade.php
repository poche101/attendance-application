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

        /* Mobile Responsive Utilities */
        @media (max-width: 768px) {
            .mobile-menu-closed {
                display: none !important;
            }
            .mobile-menu-open {
                display: flex !important;
                position: fixed;
                top: 64px;
                left: 0;
                width: 100%;
                height: calc(100vh - 64px);
                z-index: 40;
                background: white;
                flex-direction: column;
                padding: 1.5rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 flex flex-col">

{{-- Top Navigation --}}
<nav class="admin-nav shadow-sm sticky top-0 z-50">
    <div class="px-4 md:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                <img src="/images/lekki-logo.png" alt="logo" class="w-full h-full object-contain">
            </div>
            <div class="truncate">
                <span class="font-head text-lg md:text-2xl tracking-tight text-slate-900 block md:inline truncate">Sunday Service Attendance</span>
                <span class="text-blue-600 font-semibold text-sm md:text-base">Dashboard</span>
            </div>
        </div>

        {{-- Desktop Nav Links --}}
        <div class="hidden md:flex items-center gap-6 text-sm">
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

        {{-- Mobile Hamburger Button Toggle --}}
        <div class="flex md:hidden items-center">
            <button id="mobile-menu-button" type="button" class="text-slate-700 hover:text-slate-900 focus:outline-none p-2">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path id="menu-icon-hamburger" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path id="menu-icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Nav Drawer Dropdown Menu --}}
    <div id="mobile-nav-drawer" class="mobile-menu-closed hidden border-b border-slate-200 shadow-inner md:hidden">
        <div class="flex flex-col gap-2 w-full">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 pt-2">Quick Actions</span>
            <a href="{{ route('checkin') }}" class="px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 font-medium">
                Check In
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 rounded-xl {{ request()->routeIs('admin.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'hover:bg-slate-50' }} text-slate-700">
                Dashboard
            </a>

            <div class="border-t border-slate-100 my-2"></div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider px-4">Navigation Menu</span>

            @php
                $navItemsMobile = [
                    ['route' => 'admin.dashboard',      'label' => 'Dashboard',    'icon' => '⊞'],
                    ['route' => 'admin.members.index',  'label' => 'Members',      'icon' => '👥'],
                    ['route' => 'admin.rankings',       'label' => 'Top Rankings', 'icon' => '🏆'],
                    ['route' => 'admin.export',         'label' => 'Export Data',  'icon' => '↓'],
                ];
            @endphp

            @foreach($navItemsMobile as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 {{ request()->routeIs($item['route'] . '*') ? 'bg-slate-100 text-blue-700 font-semibold' : 'hover:bg-slate-50' }}">
                    <span class="text-lg">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

            <div class="border-t border-slate-100 my-2"></div>
            <form method="POST" action="{{ route('logout') }}" class="w-full px-4 pb-4">
                @csrf
                <button type="submit" class="w-full text-left py-3 text-red-600 font-medium hover:text-red-700 focus:outline-none">
                    Sign out
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- Toast Notification --}}
@if(session('toast'))
    <div class="fixed bottom-6 right-6安全 z-[200] px-6 py-4 rounded-2xl text-sm shadow-xl flex items-center gap-3 bg-emerald-100 text-emerald-800 border border-emerald-200 max-w-sm">
        {{ session('toast') }}
    </div>
@endif

{{-- Layout Main Flex Wrapper --}}
<div class="flex flex-col md:flex-row flex-1" style="min-height: calc(100vh - 64px);">

    {{-- Desktop Left Sidebar Menu Panel --}}
    <aside class="hidden md:block w-56 bg-white border-r border-slate-200 flex-shrink-0 py-8 px-5">
        @php
            $navItems = [
                ['route' => 'admin.dashboard',      'label' => 'Dashboard',    'icon' => '⊞'],
                ['route' => 'admin.members.index',  'label' => 'Members',      'icon' => '👥'],
                ['route' => 'admin.rankings',       'label' => 'Top Rankings', 'icon' => '🏆'],
                ['route' => 'admin.export',         'label' => 'Export Data',  'icon' => '↓'],
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

    {{-- Main Dynamic Workspace Area --}}
    <main class="flex-1 p-4 md:p-8 overflow-x-hidden overflow-y-auto w-full">
        @yield('main')
    </main>
</div>

{{-- Footer --}}
<footer class="bg-white border-t border-slate-200 py-5 text-center text-xs text-slate-500 w-full mt-auto">
    Christ Embassy Lekki Attendance System © {{ date('Y') }} • All Rights Reserved
</footer>

{{-- Script logic implementation for Mobile hamburger toggle --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuBtn = document.getElementById('mobile-menu-button');
        const drawer = document.getElementById('mobile-nav-drawer');
        const hamburgerIcon = document.getElementById('menu-icon-hamburger');
        const closeIcon = document.getElementById('menu-icon-close');

        if (menuBtn && drawer) {
            menuBtn.addEventListener('click', function () {
                const isOpen = drawer.classList.contains('mobile-menu-open');

                if (isOpen) {
                    drawer.classList.remove('mobile-menu-open');
                    drawer.classList.add('mobile-menu-closed', 'hidden');
                    hamburgerIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                } else {
                    drawer.classList.remove('mobile-menu-closed', 'hidden');
                    drawer.classList.add('mobile-menu-open');
                    hamburgerIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                }
            });
        }
    });
</script>

@stack('scripts')
</body>
</html>
