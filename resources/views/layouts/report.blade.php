<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'snitch | Report')</title>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-0DN83YGKTX"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-0DN83YGKTX');
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4f46e5",
                        "background-light": "#f8f6f6",
                        "background-dark": "#0f172a",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; display: inline-block; line-height: 1; font-size: 24px; }
        .code-grid-bg { background-image: radial-gradient(circle at 2px 2px, rgba(236, 91, 19, 0.05) 1px, transparent 0); background-size: 40px 40px; }
    </style>
    @stack('styles')
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
<div class="relative min-h-screen flex flex-col overflow-x-hidden code-grid-bg">
    <!-- Header -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between z-10 border-b border-slate-200 dark:border-slate-800 mb-8">
        <div class="flex items-center gap-3">
            <a href="/" class="flex items-center gap-3">
                <div class="bg-primary p-1.5 rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-2xl font-bold">radar</span>
                </div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-900 dark:text-slate-100 uppercase">snitch</h1>
            </a>
        </div>
        <nav class="flex items-center gap-6">
            <a href="{{ route('report.show', $report->uuid) }}" 
               class="text-sm font-bold {{ Route::currentRouteName() === 'report.show' ? 'text-primary' : 'text-slate-500 hover:text-slate-900 dark:hover:text-slate-100' }}">
               Technical Deep Dive
            </a>
            <a href="{{ route('report.business', $report->uuid) }}" 
               class="text-sm font-bold {{ Route::currentRouteName() === 'report.business' ? 'text-primary' : 'text-slate-500 hover:text-slate-900 dark:hover:text-slate-100' }}">
               Business Insights
            </a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-6 pb-12 z-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="text-sm text-slate-500 dark:text-slate-500 font-medium">
                © 2026 snitch. All rights reserved.
            </p>
        </div>
    </footer>
</div>
@stack('scripts')
</body>
</html>
