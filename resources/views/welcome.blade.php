<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Snitch | Automated Code Analysis</title>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-0DN83YGKTX"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-0DN83YGKTX');
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <style type="text/css">
        @font-face {font-family:Inter;font-style:normal;font-weight:400;src:url(/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:400;src:url(/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);unicode-range:U+0100-02AF,U+0304,U+0308,U+0329,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:400;src:url(/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);unicode-range:U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:400;src:url(/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);unicode-range:U+0370-03FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:400;src:url(/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);unicode-range:U+1F00-1FFF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:400;src:url(/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:400;src:url(/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);unicode-range:U+0460-052F,U+1C80-1C88,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:500;src:url(/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);unicode-range:U+0370-03FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:500;src:url(/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:500;src:url(/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);unicode-range:U+0460-052F,U+1C80-1C88,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:500;src:url(/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);unicode-range:U+1F00-1FFF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:500;src:url(/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);unicode-range:U+0100-02AF,U+0304,U+0308,U+0329,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:500;src:url(/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);unicode-range:U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:500;src:url(/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:600;src:url(/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);unicode-range:U+1F00-1FFF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:600;src:url(/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:600;src:url(/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);unicode-range:U+0460-052F,U+1C80-1C88,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:600;src:url(/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);unicode-range:U+0100-02AF,U+0304,U+0308,U+0329,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:600;src:url(/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);unicode-range:U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:600;src:url(/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);unicode-range:U+0370-03FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:600;src:url(/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:700;src:url(/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:700;src:url(/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);unicode-range:U+0100-02AF,U+0304,U+0308,U+0329,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:700;src:url(/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);unicode-range:U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:700;src:url(/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);unicode-range:U+0370-03FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:700;src:url(/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:700;src:url(/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);unicode-range:U+1F00-1FFF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:700;src:url(/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);unicode-range:U+0460-052F,U+1C80-1C88,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:800;src:url(/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);unicode-range:U+0100-02AF,U+0304,U+0308,U+0329,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:800;src:url(/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);unicode-range:U+0370-03FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:800;src:url(/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);unicode-range:U+1F00-1FFF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:800;src:url(/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:800;src:url(/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);unicode-range:U+0460-052F,U+1C80-1C88,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:800;src:url(/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:800;src:url(/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);unicode-range:U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:900;src:url(/cf-fonts/v/inter/5.0.16/greek/wght/normal.woff2);unicode-range:U+0370-03FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:900;src:url(/cf-fonts/v/inter/5.0.16/cyrillic-ext/wght/normal.woff2);unicode-range:U+0460-052F,U+1C80-1C88,U+20B4,U+2DE0-2DFF,U+A640-A69F,U+FE2E-FE2F;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:900;src:url(/cf-fonts/v/inter/5.0.16/latin-ext/wght/normal.woff2);unicode-range:U+0100-02AF,U+0304,U+0308,U+0329,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20CF,U+2113,U+2C60-2C7F,U+A720-A7FF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:900;src:url(/cf-fonts/v/inter/5.0.16/greek-ext/wght/normal.woff2);unicode-range:U+1F00-1FFF;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:900;src:url(/cf-fonts/v/inter/5.0.16/cyrillic/wght/normal.woff2);unicode-range:U+0301,U+0400-045F,U+0490-0491,U+04B0-04B1,U+2116;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:900;src:url(/cf-fonts/v/inter/5.0.16/latin/wght/normal.woff2);unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;font-display:swap;}
        @font-face {font-family:Inter;font-style:normal;font-weight:900;src:url(/cf-fonts/v/inter/5.0.16/vietnamese/wght/normal.woff2);unicode-range:U+0102-0103,U+0110-0111,U+0128-0129,U+0168-0169,U+01A0-01A1,U+01AF-01B0,U+0300-0301,U+0303-0304,U+0308-0309,U+0323,U+0329,U+1EA0-1EF9,U+20AB;font-display:swap;}
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4f46e5", // Indigo/Cyber Blue primary
                        "background-light": "#f8f6f6",
                        "background-dark": "#0f172a", // Deep slate/indigo dark background
                        "accent-orange": "#f97316",
                        "accent-green": "#10b981"
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
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            font-size: 24px;
        }
        .code-grid-bg {
            background-image: radial-gradient(circle at 2px 2px, rgba(236, 91, 19, 0.05) 1px, transparent 0);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
<div class="relative min-h-screen flex flex-col overflow-x-hidden code-grid-bg">
    <!-- Header -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between z-10">
        <div class="flex items-center gap-3">
            <div class="bg-primary p-1.5 rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-2xl font-bold">radar</span>
            </div>
            <h1 class="text-2xl font-black tracking-tighter text-slate-900 dark:text-slate-100 uppercase">Snitch</h1>
        </div>
        <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600 dark:text-slate-400">
            <a class="hover:text-primary transition-colors" href="#">Features</a>
            <a class="hover:text-primary transition-colors" href="#">Pricing</a>
            <a class="hover:text-primary transition-colors" href="#">Docs</a>
        </nav>
        <div class="flex items-center gap-4">
            <button class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-primary transition-colors">Log In</button>
            <button class="bg-primary p-1.5 rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary/20">
                Get Started
            </button>
        </div>
    </header>
    <!-- Hero Section -->
    <main class="flex-grow flex flex-col items-center justify-center px-6 py-12 lg:py-24 relative">
        <!-- Background Decoration -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-96 bg-primary/10 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="max-w-4xl w-full text-center flex flex-col items-center z-10">
            <h2 class="text-5xl md:text-7xl font-black leading-[1.1] tracking-tight text-slate-900 dark:text-slate-100 mb-6">Analyze your PHP codebase in <span class="text-primary">seconds.</span></h2>
            <!-- Main Action Area -->
            <div class="w-full max-w-4xl bg-white dark:bg-slate-900/50 backdrop-blur-xl p-2 rounded-xl border border-slate-200 dark:border-slate-800 shadow-2xl">
                <div class="flex flex-col md:flex-row gap-2">
                    <div class="flex-grow relative">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined">link</span>
                        </div>
                        <input id="repo-input" class="w-full bg-slate-50 dark:bg-slate-800/50 border-none focus:ring-2 focus:ring-primary/50 rounded-lg py-4 pl-12 pr-4 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 font-medium" placeholder="Paste your GitHub or GitLab URL here..." type="text"/>
                    </div>
                    <button id="analyze-btn" class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-lg font-bold transition-all flex items-center justify-center gap-2 whitespace-nowrap shadow-lg shadow-primary/30">
                        Analyze Repository
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </div>
            </div>
            <div id="error-message" class="text-red-500 text-sm mt-2 hidden text-left w-full max-w-4xl px-2">
                Invalid link
            </div>
            <!-- Social Proof / Support -->
        </div>
    </main>
    <!-- Feature Grid -->
    <!-- Preview Section -->
    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="text-sm text-slate-500 dark:text-slate-500 font-medium">
                © 2024 Snitch AI. All rights reserved.
            </p>
            <div class="flex items-center gap-6">
                <a class="text-slate-400 hover:text-primary transition-colors text-xs font-semibold" href="#">Privacy</a>
                <a class="text-slate-400 hover:text-primary transition-colors text-xs font-semibold" href="#">Terms</a>
            </div>
        </div>
    </footer>
</div>
<script>
    document.getElementById('analyze-btn').addEventListener('click', async () => {
        const repoUrl = document.getElementById('repo-input').value;
        const errorDiv = document.getElementById('error-message');
        const analyzeBtn = document.getElementById('analyze-btn');

        if (!repoUrl) {
            errorDiv.classList.remove('hidden');
            return;
        }

        analyzeBtn.disabled = true;
        analyzeBtn.innerHTML = 'Analyzing...';
        errorDiv.classList.add('hidden');
        errorDiv.innerText = 'Invalid link';

        try {
            const response = await fetch('/analyze', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ url: repoUrl })
            });

            const result = await response.json();

            if (!response.ok) {
                errorDiv.innerText = result.error + (result.details ? ': ' + result.details : '');
                errorDiv.classList.remove('hidden');
            } else {
                if (!result.valid) {
                    errorDiv.innerText = result.error + (result.details ? ': ' + result.details : '');
                    errorDiv.classList.remove('hidden');
                } else {
                    // Success, redirect to the report page
                    window.location.href = result.redirect_url;
                }
            }
        } catch (error) {
            errorDiv.classList.remove('hidden');
        } finally {
            analyzeBtn.disabled = false;
            analyzeBtn.innerHTML = 'Analyze Repository <span class="material-symbols-outlined">arrow_forward</span>';
        }
    });
</script>
</body>
</html>
