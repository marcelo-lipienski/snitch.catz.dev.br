<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Snitch | Report {{ $report->uuid }}</title>
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
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
<div class="relative min-h-screen flex flex-col overflow-x-hidden code-grid-bg">
    <!-- Header -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between z-10">
        <div class="flex items-center gap-3">
            <a href="/" class="flex items-center gap-3">
                <div class="bg-primary p-1.5 rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-2xl font-bold">radar</span>
                </div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-900 dark:text-slate-100 uppercase">Snitch</h1>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col items-center justify-center px-6 py-12 relative">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-96 bg-primary/10 blur-[120px] rounded-full pointer-events-none"></div>
        
        <div class="max-w-4xl w-full text-center flex flex-col items-center z-10">
            <div class="mb-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary text-sm font-bold mb-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    Analyzing your application
                </div>
                <h2 class="text-4xl md:text-5xl font-black leading-tight text-slate-900 dark:text-slate-100 mb-4">
                    Sniffing through your <span class="text-primary text-break">{{ $report->repository_url }}</span>
                </h2>
                <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto text-lg">
                    We're currently scanning your repository for patterns, potential issues, and optimization opportunities. This will only take a moment.
                </p>
            </div>

            <!-- Analysis Progress Placeholder -->
            <div class="w-full bg-white dark:bg-slate-900/50 backdrop-blur-xl p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl">
                <div class="space-y-6">
                    <div class="flex items-center justify-between text-sm font-bold">
                        <span class="text-slate-400">Current Phase</span>
                        <span class="text-primary">Repository Scan</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden">
                        <div class="bg-primary h-full rounded-full animate-[progress_2s_ease-in-out_infinite]" style="width: 30%"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status</div>
                            <div id="status-text" class="text-sm font-bold text-accent-orange">{{ ucfirst($report->status) }}</div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Report ID</div>
                            <div class="text-sm font-bold text-slate-600 dark:text-slate-300 truncate">{{ $report->uuid }}</div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Files Found</div>
                            <div class="text-sm font-bold text-slate-600 dark:text-slate-300">Scanning...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        @keyframes progress {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(350%); }
        }
    </style>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="text-sm text-slate-500 dark:text-slate-500 font-medium">
                © 2024 Snitch AI. All rights reserved.
            </p>
        </div>
    </footer>
</div>

<script>
    const uuid = "{{ $report->uuid }}";
    const pollStatus = async () => {
        try {
            const response = await fetch(`/report/${uuid}/status`);
            const data = await response.json();
            
            if (data.is_completed) {
                window.location.reload();
            } else if (data.status === 'failed') {
                document.getElementById('status-text').innerText = 'Failed';
                document.getElementById('status-text').classList.add('text-red-500');
            } else {
                setTimeout(pollStatus, 3000); // Poll every 3 seconds
            }
        } catch (error) {
            console.error('Error polling status:', error);
            setTimeout(pollStatus, 5000);
        }
    };

    if ("{{ $report->status }}" !== 'completed' && "{{ $report->status }}" !== 'failed') {
        pollStatus();
    }
</script>
</body>
</html>
