@extends('layouts.report')

@section('title', 'Snitch | Technical Report - ' . $report->uuid)

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 dark:text-slate-100">Technical Deep Dive</h2>
            <p class="text-slate-500 dark:text-slate-400">In-depth analysis of code patterns, architecture, and technical debt.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">
                {{ $report->status }}
            </span>
            <span class="text-sm text-slate-500 font-mono">{{ substr($report->commit_hash, 0, 7) }}</span>
        </div>
    </div>

    <!-- Key Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- System Health -->
        <div class="bg-white dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="material-symbols-outlined text-6xl text-primary">favorite</span>
            </div>
            <div class="relative size-28 rounded-full flex items-center justify-center mb-4">
                <svg class="size-full -rotate-90" viewbox="0 0 100 100">
                    <circle class="text-slate-100 dark:text-slate-800" cx="50" cy="50" fill="transparent" r="44" stroke="currentColor" stroke-width="8"></circle>
                    <circle class="text-primary" cx="50" cy="50" fill="transparent" r="44" stroke="currentColor" stroke-dasharray="276" stroke-dashoffset="{{ 276 - (276 * ($dummyData['technical']['system_health'] ?? 0) / 100) }}" stroke-width="8" stroke-linecap="round"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ $dummyData['technical']['system_health'] }}%</span>
                </div>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">System Health</span>
        </div>

        <!-- Risk Profile -->
        <div class="bg-white dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 rounded-lg bg-rose-500/10 text-rose-500">
                    <span class="material-symbols-outlined">gpp_maybe</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Risk Profile</span>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-slate-100 mb-1">{{ $dummyData['technical']['risk_profile'] }}</div>
            <div class="text-xs text-slate-500">Score: {{ $dummyData['technical']['risk_score'] }}/100</div>
            <div class="mt-4 w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $dummyData['technical']['risk_score'] }}%"></div>
            </div>
        </div>

        <!-- Debt Recovery -->
        <div class="bg-white dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 rounded-lg bg-amber-500/10 text-amber-500">
                    <span class="material-symbols-outlined">hourglass_empty</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Debt Recovery</span>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-slate-100 mb-1">{{ $dummyData['technical']['debt_recovery'] }}</div>
            <div class="text-xs text-slate-500">Estimated Effort</div>
            <div class="mt-4 w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                <div class="bg-amber-500 h-1.5 rounded-full" style="width: 40%"></div>
            </div>
        </div>

        <!-- Maintainability -->
        <div class="bg-white dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-500">
                    <span class="material-symbols-outlined">auto_fix_high</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Maintainability</span>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-slate-100 mb-1">{{ $dummyData['technical']['maintainability_index'] }}%</div>
            <div class="text-xs text-slate-500">Index Score</div>
            <div class="mt-4 w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $dummyData['technical']['maintainability_index'] }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Findings -->
        <div class="bg-white dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">Key Findings</h3>
            </div>
            <div class="divide-y divide-slate-200 dark:divide-slate-800">
                @foreach($dummyData['technical']['findings'] ?? [] as $finding)
                <div class="p-6 flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center {{ $finding['severity'] === 'high' ? 'bg-rose-500/10 text-rose-500' : ($finding['severity'] === 'medium' ? 'bg-amber-500/10 text-amber-500' : 'bg-blue-500/10 text-blue-500') }}">
                        <span class="material-symbols-outlined">{{ $finding['icon'] }}</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-slate-100">{{ $finding['title'] }}</h4>
                        <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">{{ $finding['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Complexity & Duplication -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-900/50 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mb-6">Structural Metrics</h3>
                <div class="space-y-8">
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-bold text-slate-500 uppercase tracking-widest">Complexity Score</span>
                            <span class="text-lg font-black text-slate-900 dark:text-slate-100">{{ $dummyData['technical']['complexity_score'] }}/100</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full rounded-full" style="width: {{ $dummyData['technical']['complexity_score'] }}%"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-bold text-slate-500 uppercase tracking-widest">Duplication Score</span>
                            <span class="text-lg font-black text-slate-900 dark:text-slate-100">{{ $dummyData['technical']['duplication_score'] }}/100</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ $dummyData['technical']['duplication_score'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
