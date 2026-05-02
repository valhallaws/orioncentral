<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        theme: localStorage.getItem('theme') || 'system',
        init() {
            this.applyTheme(this.theme);
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (this.theme === 'system') this.applyTheme('system');
            });
        },
        applyTheme(theme) {
            this.theme = theme;
            localStorage.setItem('theme', theme);

            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }"
    class="antialiased"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <!-- Prevent FOUC -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-secondary-50 text-secondary-900 dark:bg-secondary-950 dark:text-neutral-200 h-screen flex overflow-hidden transition-colors duration-200" x-data="{ mobileMenuOpen: false }">

        <!-- Sidebar (Desktop - lg and up) -->
        <aside class="hidden lg:flex flex-col w-64 bg-slate-800 dark:bg-secondary-900 border-r border-slate-700 dark:border-secondary-800 z-20 flex-shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-slate-700 dark:border-secondary-800">
                <span class="rounded-full p-1 bg-slate-900 dark:bg-slate-800 mr-3">
                    <img src="{{ asset('storage/logos/Isotipo.png') }}" alt="OrionCentral Logo" class="h-7 w-auto">
                </span>
                <span class="text-xl font-bold text-primary-500 tracking-wide">
                    ORION
                </span>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
                <!-- Ejemplo de enlaces del Sidebar -->
                <a href="{{ route('home') }}" class="flex items-center px-3 py-2.5 bg-primary-500/10 text-primary-500 rounded-lg group font-medium transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <a href="{{ route('clientes') }}" class="flex items-center px-3 py-2.5 text-slate-300 hover:bg-slate-700 hover:text-white dark:text-neutral-400 dark:hover:bg-secondary-800 dark:hover:text-neutral-200 rounded-lg group font-medium transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white dark:text-neutral-500 dark:group-hover:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Clientes
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 text-slate-300 hover:bg-slate-700 hover:text-white dark:text-neutral-400 dark:hover:bg-secondary-800 dark:hover:text-neutral-200 rounded-lg group font-medium transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white dark:text-neutral-500 dark:group-hover:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    Instancias
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 text-slate-300 hover:bg-slate-700 hover:text-white dark:text-neutral-400 dark:hover:bg-secondary-800 dark:hover:text-neutral-200 rounded-lg group font-medium transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white dark:text-neutral-500 dark:group-hover:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                    Respaldos
                </a>
            </nav>
        </aside>

        <!-- Off-canvas Mobile Menu (sm & xs) -->
        <div x-show="mobileMenuOpen" class="relative z-40 lg:hidden" x-ref="dialog" aria-modal="true">
            <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>

            <div class="fixed inset-0 flex">
                <div x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative mr-16 flex w-full max-w-xs flex-1">

                    <div x-show="mobileMenuOpen" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button type="button" @click="mobileMenuOpen = false" class="-m-2.5 p-2.5">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Mobile Sidebar Content -->
                    <div class="flex flex-col w-full bg-slate-800 dark:bg-secondary-900 overflow-y-auto">
                        <div class="h-16 flex items-center px-6 border-b border-slate-700 dark:border-secondary-800">
                            <span class="rounded-full p-1 bg-slate-900 dark:bg-slate-800 mr-3">
                                <img src="{{ asset('storage/logos/Isotipo.png') }}" alt="OrionCentral Logo" class="h-7 w-auto">
                            </span>
                            <span class="text-xl font-bold text-primary-500 tracking-wide">
                                ORION
                            </span>
                        </div>
                        <nav class="flex-1 py-6 px-4 space-y-2">
                            <a href="#" class="flex items-center px-3 py-2.5 bg-primary-500/10 text-primary-500 rounded-lg group font-medium transition-colors">
                                Dashboard
                            </a>
                            <a href="#" class="flex items-center px-3 py-2.5 text-slate-300 hover:bg-slate-700 hover:text-white rounded-lg group font-medium transition-colors">
                                Clientes
                            </a>
                            <a href="#" class="flex items-center px-3 py-2.5 text-slate-300 hover:bg-slate-700 hover:text-white rounded-lg group font-medium transition-colors">
                                Instancias
                            </a>
                            <a href="#" class="flex items-center px-3 py-2.5 text-slate-300 hover:bg-slate-700 hover:text-white rounded-lg group font-medium transition-colors">
                                Respaldos
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-secondary-50 dark:bg-secondary-950">

            <!-- Topbar -->
            <header class="bg-white dark:bg-secondary-900 shadow-sm border-b border-secondary-200 dark:border-secondary-800 z-10 flex-shrink-0">
                <div class="w-full px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

                    <!-- Left Section: Mobile Menu Button & Horizontal Links -->
                    <div class="flex items-center">
                        <!-- Hamburger Menu (sm & xs only, hidden on md+) -->
                        <button @click="mobileMenuOpen = true" type="button" class="md:hidden p-2 -ml-2 mr-3 text-secondary-500 hover:text-secondary-700 dark:text-neutral-400 dark:hover:text-neutral-200 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        </button>

                        <!-- Logo for md screens (when sidebar is hidden but horizontal menu is shown) -->
                        <div class="hidden md:flex lg:hidden items-center space-x-3 mr-6">
                            <span class="rounded-full p-1 bg-slate-800 dark:bg-slate-600">
                                <img src="{{ asset('storage/logos/Isotipo.png') }}" alt="Orion Logo" class="h-6 w-auto">
                            </span>
                        </div>

                        <!-- Horizontal Menu (Visible only on md screens: md:flex lg:hidden) -->
                        <nav class="hidden md:flex lg:hidden space-x-1">
                            <a href="#" class="bg-primary-50 text-primary-600 dark:bg-secondary-800 dark:text-primary-400 px-3 py-2 text-sm font-medium rounded-md">Dashboard</a>
                            <a href="#" class="text-secondary-600 hover:bg-secondary-100 hover:text-secondary-900 dark:text-neutral-400 dark:hover:bg-secondary-800 dark:hover:text-neutral-200 px-3 py-2 text-sm font-medium rounded-md transition-colors">Clientes</a>
                            <a href="#" class="text-secondary-600 hover:bg-secondary-100 hover:text-secondary-900 dark:text-neutral-400 dark:hover:bg-secondary-800 dark:hover:text-neutral-200 px-3 py-2 text-sm font-medium rounded-md transition-colors">Instancias</a>
                            <a href="#" class="text-secondary-600 hover:bg-secondary-100 hover:text-secondary-900 dark:text-neutral-400 dark:hover:bg-secondary-800 dark:hover:text-neutral-200 px-3 py-2 text-sm font-medium rounded-md transition-colors">Respaldos</a>
                        </nav>
                    </div>

                    <!-- Right Section: Theme Selector -->
                    <div class="flex items-center space-x-4">
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" type="button" class="p-2 rounded-md text-secondary-500 hover:text-secondary-900 dark:text-neutral-400 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                                <!-- Icon: System -->
                                <svg x-show="theme === 'system'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <!-- Icon: Light -->
                                <svg x-cloak x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <!-- Icon: Dark -->
                                <svg x-cloak x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            </button>

                            <!-- Dropdown menu -->
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-36 rounded-md shadow-lg bg-white dark:bg-secondary-800 ring-1 ring-black ring-opacity-5 divide-y divide-secondary-100 dark:divide-secondary-700 focus:outline-none z-50"
                                style="display: none;">
                                <div class="py-1">
                                    <button @click="applyTheme('light'); open = false" class="group flex items-center w-full px-4 py-2 text-sm text-secondary-700 dark:text-neutral-300 hover:bg-secondary-50 dark:hover:bg-secondary-700">
                                        <svg class="mr-3 h-5 w-5 text-neutral-400 group-hover:text-neutral-500 dark:text-neutral-500 dark:group-hover:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        Claro
                                    </button>
                                    <button @click="applyTheme('dark'); open = false" class="group flex items-center w-full px-4 py-2 text-sm text-secondary-700 dark:text-neutral-300 hover:bg-secondary-50 dark:hover:bg-secondary-700">
                                        <svg class="mr-3 h-5 w-5 text-neutral-400 group-hover:text-neutral-500 dark:text-neutral-500 dark:group-hover:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                                        Oscuro
                                    </button>
                                    <button @click="applyTheme('system'); open = false" class="group flex items-center w-full px-4 py-2 text-sm text-secondary-700 dark:text-neutral-300 hover:bg-secondary-50 dark:hover:bg-secondary-700">
                                        <svg class="mr-3 h-5 w-5 text-neutral-400 group-hover:text-neutral-500 dark:text-neutral-500 dark:group-hover:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        Sistema
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto w-full p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
