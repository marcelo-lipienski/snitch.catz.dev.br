@extends('layouts.report')

@section('title', 'snitch | Technical Report - ' . $report->uuid)
@section('header_title', 'Technical Deep Dive')
@section('header_description', 'In-depth analysis of code patterns, architecture, and technical debt.')

@section('content')
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
                    <circle class="text-primary" cx="50" cy="50" fill="transparent" r="44" stroke="currentColor" stroke-dasharray="276" stroke-dashoffset="{{ 276 - (276 * ($reportData['technical']['system_health'] ?? 0) / 100) }}" stroke-width="8" stroke-linecap="round"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ $reportData['technical']['system_health'] }}%</span>
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
            <div class="text-3xl font-black text-slate-900 dark:text-slate-100 mb-1">{{ $reportData['technical']['risk_profile'] }}</div>
            <div class="text-xs text-slate-500">Score: {{ $reportData['technical']['risk_score'] }}/100</div>
            <div class="mt-4 w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $reportData['technical']['risk_score'] }}%"></div>
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
            <div class="text-3xl font-black text-slate-900 dark:text-slate-100 mb-1">{{ $reportData['technical']['debt_recovery'] }}</div>
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
            <div class="text-3xl font-black text-slate-900 dark:text-slate-100 mb-1">{{ $reportData['technical']['maintainability_index'] }}%</div>
            <div class="text-xs text-slate-500">Index Score</div>
            <div class="mt-4 w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5">
                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $reportData['technical']['maintainability_index'] }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        <!-- File Tree -->
        <div class="md:col-span-4 bg-white dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col h-[600px]">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">Files with Issues</h3>
            </div>
            <div class="flex-grow overflow-y-auto p-4 custom-scrollbar">
                @php
                    $renderTree = function($tree) use (&$renderTree) {
                        ksort($tree);
                        foreach ($tree as $name => $node) {
                            $is_file = $node['_is_file'];
                            $children = $node['_children'];
                            
                            if ($is_file) {
                                echo '<div class="file-item group flex items-center justify-between p-2 rounded-lg hover:bg-primary/10 cursor-pointer transition-colors" data-file="'.$node['_full_path'].'">';
                                echo '<div class="flex items-center gap-2 overflow-hidden">';
                                echo '<span class="material-symbols-outlined text-slate-400 text-sm">description</span>';
                                echo '<span class="text-sm truncate text-slate-600 dark:text-slate-400 group-hover:text-primary">'.$name.'</span>';
                                echo '</div>';
                                echo '<span class="text-[10px] font-bold bg-rose-500/10 text-rose-500 px-1.5 py-0.5 rounded">'.$node['_issue_count'].'</span>';
                                echo '</div>';
                            } else {
                                echo '<div class="folder-item mb-1">';
                                echo '<div class="flex items-center gap-2 p-2 text-sm font-bold text-slate-500">';
                                echo '<span class="material-symbols-outlined text-sm">folder</span>';
                                echo $name;
                                echo '</div>';
                                echo '<div class="ml-4 border-l border-slate-200 dark:border-slate-800 pl-2">';
                                $renderTree($children);
                                echo '</div>';
                                echo '</div>';
                            }
                        }
                    };
                @endphp
                
                <div id="file-tree-root">
                    {!! $renderTree($reportData['technical']['file_tree'] ?? []) !!}
                </div>
            </div>
        </div>

        <!-- Issue List (Replaces Structural Metrics) -->
        <div class="md:col-span-8 bg-white dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col h-[600px]">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">Issue Details</h3>
                    <p id="selected-file-name" class="text-xs text-slate-500 font-mono mt-1">Select a file to see findings</p>
                </div>
                <div id="issue-count-badge" class="hidden px-3 py-1 rounded-full bg-rose-500/10 text-rose-500 text-xs font-bold">
                    0 Issues
                </div>
            </div>
            
            <div id="issues-container" class="flex-grow overflow-y-auto custom-scrollbar">
                <div class="h-full flex flex-col items-center justify-center text-center p-12 text-slate-400">
                    <span class="material-symbols-outlined text-6xl mb-4 opacity-20">find_in_page</span>
                    <p>Select a file from the tree to explore detected issues and technical debt findings.</p>
                </div>
            </div>
        </div>
    </div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
    .file-item.active { background-color: rgb(79 70 229 / 0.1); }
    .file-item.active span { color: #4f46e5; }
</style>

@push('scripts')
<script>
    const findings = @json($reportData['technical']['findings'] ?? []);
    
    document.querySelectorAll('.file-item').forEach(item => {
        item.addEventListener('click', () => {
            const fileName = item.getAttribute('data-file');
            const fileIssues = findings[fileName] || [];
            
            // Update active state
            document.querySelectorAll('.file-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            
            // Update header
            document.getElementById('selected-file-name').innerText = fileName;
            const badge = document.getElementById('issue-count-badge');
            badge.innerText = `${fileIssues.length} ${fileIssues.length === 1 ? 'Issue' : 'Issues'}`;
            badge.classList.remove('hidden');
            
            // Update issues list
            const container = document.getElementById('issues-container');
            if (fileIssues.length === 0) {
                container.innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-center p-12 text-slate-400">
                        <span class="material-symbols-outlined text-6xl mb-4 opacity-20">check_circle</span>
                        <p>No issues found in this file.</p>
                    </div>`;
                return;
            }
            
            let html = '<div class="divide-y divide-slate-200 dark:divide-slate-800">';
            fileIssues.forEach(issue => {
                const severityClass = issue.severity === 'high' ? 'bg-rose-500/10 text-rose-500' : 
                                     (issue.severity === 'medium' ? 'bg-amber-500/10 text-amber-500' : 'bg-blue-500/10 text-blue-500');
                
                html += `
                    <div class="p-6 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center ${severityClass}">
                                <span class="material-symbols-outlined">${issue.icon}</span>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between gap-4 mb-1">
                                    <h4 class="font-bold text-slate-900 dark:text-slate-100">${issue.title}</h4>
                                    ${issue.line ? `<span class="text-[10px] font-mono bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-500">Line ${issue.line}</span>` : ''}
                                </div>
                                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">${issue.description}</p>
                            </div>
                        </div>
                    </div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        });
    });
</script>
@endpush
@endsection
