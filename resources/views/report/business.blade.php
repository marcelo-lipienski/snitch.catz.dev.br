@extends('layouts.report')

@section('title', 'Snitch | Business Report - ' . $report->uuid)

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 dark:text-slate-100">Business Insights</h2>
            <p class="text-slate-500 dark:text-slate-400">Analysis of team productivity, delivery speed, and operational efficiency.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">
                {{ $report->status }}
            </span>
            <span class="text-sm text-slate-500 font-mono">{{ substr($report->commit_hash, 0, 7) }}</span>
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="bg-white dark:bg-slate-900/50 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 border-l-4 border-l-primary shadow-sm">
        <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">description</span>
            Strategic Executive Summary
        </h3>
        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
            {{ $dummyData['business']['summary'] }}
        </p>
    </div>

    <!-- Top Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white dark:bg-slate-900/50 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">Roadmap Opportunity Cost</p>
                <span class="material-symbols-outlined text-amber-500">monetization_on</span>
            </div>
            <h3 class="text-4xl font-black text-amber-500">{{ $dummyData['business']['roadmap_opportunity_cost'] }}</h3>
            <p class="text-xs text-slate-500 mt-4 leading-relaxed">
                This debt represents <span class="font-bold text-slate-900 dark:text-slate-100">1.0 weeks</span> of direct feature delay.
            </p>
        </div>
        <div class="bg-white dark:bg-slate-900/50 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">Governance & Liability</p>
                <span class="material-symbols-outlined text-rose-500">gavel</span>
            </div>
            <h3 class="text-4xl font-black text-rose-500">{{ $dummyData['business']['governance_liability'] }}</h3>
            <p class="text-xs text-slate-500 mt-4 leading-relaxed">
                Exposure includes <span class="font-bold text-rose-500">0 Compliance Liabilities</span>.
            </p>
        </div>
        <div class="bg-white dark:bg-slate-900/50 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">Feature Velocity Index</p>
                <span class="material-symbols-outlined text-primary">trending_up</span>
            </div>
            <h3 class="text-4xl font-black text-primary">{{ $dummyData['business']['feature_velocity_index'] }}</h3>
            <p class="text-xs text-slate-500 mt-4 leading-relaxed">
                Measure of organizational agility.
            </p>
        </div>
    </div>

    <!-- Detailed Risks and Interest -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Enterprise Risk Dimensions -->
        <div class="bg-white dark:bg-slate-900/50 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <h4 class="text-lg font-bold mb-6 text-slate-400 italic">Enterprise Risk Dimensions</h4>
            <div class="space-y-6">
                @foreach($dummyData['business']['risk_dimensions'] as $risk)
                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ $risk['label'] }}</span>
                        <span class="text-xs font-bold">{{ $risk['value'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-primary h-full rounded-full" style="width: {{ $risk['value'] }}%"></div>
                    </div>
                    <p class="text-[9px] text-slate-500 italic">{{ $risk['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Calculated Technical Interest -->
        <div class="bg-white dark:bg-slate-900/50 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <h4 class="text-lg font-bold mb-6 text-slate-400 italic">Calculated Technical Interest</h4>
            <div class="space-y-6">
                @foreach($dummyData['business']['technical_interest'] as $interest)
                <div class="group">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-medium capitalize">{{ $interest['label'] }}</span>
                        <span class="text-[10px] font-bold text-slate-500">{{ $interest['blocks'] }} delivery blocks</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                        <div class="bg-primary h-full transition-all group-hover:bg-primary/80" style="width: {{ $interest['value'] * 3 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Hotspots -->
    <div class="bg-white dark:bg-slate-900/50 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <h4 class="text-xl font-bold mb-6 flex items-center gap-2 text-rose-500">
            <span class="material-symbols-outlined">warning_amber</span>
            Business Units at Risk
        </h4>
        <div class="space-y-4">
            @foreach($dummyData['business']['hotspots'] as $hotspot)
            <div class="p-6 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 flex justify-between items-center">
                <div>
                    <div class="text-xs font-mono text-slate-500 mb-1">{{ $hotspot['file'] }}</div>
                    <div class="flex items-center gap-3">
                        <h5 class="text-lg font-bold">Hotspot Score: {{ $hotspot['score'] }}</h5>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-rose-500/10 text-rose-500 border border-rose-500/20">Critical Asset</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-bold text-amber-500">Volatility: {{ $hotspot['volatility'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
