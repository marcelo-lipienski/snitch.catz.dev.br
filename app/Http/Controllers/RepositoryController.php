<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\Report;
use App\Jobs\AnalyzeRepositoryJob;

class RepositoryController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'url' => ['required', 'string'],
        ]);

        $url = $request->input('url');

        // Automatically prepend https:// if no protocol is provided
        if (!preg_match('/^https?:\/\//i', $url) && !preg_match('/^git@/i', $url)) {
            $url = 'https://' . $url;
        }

        try {
            // 1. Verify if it's a valid git repository (Quick check)
            $lsRemote = Process::env(['GIT_TERMINAL_PROMPT' => '0'])
                ->run(['git', 'ls-remote', '--exit-code', $url]);

            if (!$lsRemote->successful()) {
                return response()->json(['valid' => false, 'error' => 'Invalid git repository'], 422);
            }

            // Create report record
            $report = Report::create([
                'uuid' => (string) Str::uuid(),
                'repository_url' => $url,
                'status' => 'pending',
            ]);

            // Dispatch analysis job
            AnalyzeRepositoryJob::dispatch($report);

            return response()->json([
                'valid' => true,
                'redirect_url' => route('report.show', ['uuid' => $report->uuid])
            ]);

        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'error' => 'An error occurred during validation: ' . $e->getMessage()], 500);
        }
    }
}
