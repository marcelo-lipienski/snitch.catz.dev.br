@extends('layouts.report')

@section('title', 'snitch | ARCHITECTURE.md - ' . $report->uuid)
@section('header_title', 'Suggested ARCHITECTURE.md')
@section('header_description', 'Proposed architectural documentation based on codebase analysis.')

@push('header_actions')
<a href="{{ route('report.show', $report->uuid) }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-primary/10 hover:text-primary transition-all border border-slate-200 dark:border-slate-700">
    <span class="material-symbols-outlined text-sm">arrow_back</span>
    Back to Report
</a>
@endpush

@section('content')
<div class="bg-white dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
    <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-between items-center">
        <div class="flex items-center gap-2 text-slate-500">
            <span class="material-symbols-outlined text-sm">description</span>
            <span class="text-xs font-bold uppercase tracking-widest">ARCHITECTURE.md</span>
        </div>
        <div class="flex bg-slate-200 dark:bg-slate-800 p-1 rounded-lg">
            <button id="btn-rendered" class="px-4 py-1.5 rounded-md text-xs font-bold transition-all bg-white dark:bg-slate-700 text-primary shadow-sm">
                Rendered
            </button>
            <button id="btn-raw" class="px-4 py-1.5 rounded-md text-xs font-bold transition-all text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                Raw
            </button>
        </div>
    </div>
    
    <div id="content-rendered" class="p-8 prose dark:prose-invert max-w-none prose-slate prose-headings:font-black prose-a:text-primary">
        {!! (new \League\CommonMark\GithubFlavoredMarkdownConverter())->convert($markdown) !!}
    </div>
    
    <div id="content-raw" class="hidden p-8">
        <pre class="bg-slate-50 dark:bg-slate-950 p-6 rounded-xl border border-slate-200 dark:border-slate-800 overflow-x-auto text-sm font-mono text-slate-700 dark:text-slate-300 leading-relaxed">{{ $markdown }}</pre>
    </div>
</div>

@push('scripts')
<script>
    const btnRendered = document.getElementById('btn-rendered');
    const btnRaw = document.getElementById('btn-raw');
    const contentRendered = document.getElementById('content-rendered');
    const contentRaw = document.getElementById('content-raw');

    btnRendered.addEventListener('click', () => {
        btnRendered.classList.add('bg-white', 'dark:bg-slate-700', 'text-primary', 'shadow-sm');
        btnRendered.classList.remove('text-slate-500');
        
        btnRaw.classList.remove('bg-white', 'dark:bg-slate-700', 'text-primary', 'shadow-sm');
        btnRaw.classList.add('text-slate-500');
        
        contentRendered.classList.remove('hidden');
        contentRaw.classList.add('hidden');
    });

    btnRaw.addEventListener('click', () => {
        btnRaw.classList.add('bg-white', 'dark:bg-slate-700', 'text-primary', 'shadow-sm');
        btnRaw.classList.remove('text-slate-500');
        
        btnRendered.classList.remove('bg-white', 'dark:bg-slate-700', 'text-primary', 'shadow-sm');
        btnRendered.classList.add('text-slate-500');
        
        contentRaw.classList.remove('hidden');
        contentRendered.classList.add('hidden');
    });
</script>
@endpush
@endsection
