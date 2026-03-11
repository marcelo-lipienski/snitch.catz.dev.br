<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\Report;
use App\Jobs\AnalyzeRepositoryJob;
use Illuminate\Database\UniqueConstraintViolationException;

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
            // 1. Verify if it's a valid git repository and fetch latest commit hash (Quick check)
            // -c core.hooksPath=/dev/null: Ensure no hooks are executed
            // --config protocol.allow=always: standard protocols (though we restrict to http(s)/git@ above)
            $lsRemote = Process::env(['GIT_TERMINAL_PROMPT' => '0'])
                ->run([
                    'git', 
                    '-c', 'core.hooksPath=/dev/null',
                    'ls-remote', 
                    '--quiet',
                    $url, 
                    'HEAD'
                ]);

            if (!$lsRemote->successful() || empty($lsRemote->output())) {
                return response()->json(['valid' => false, 'error' => 'Invalid git repository or branch'], 422);
            }

            // Extract hash from ls-remote output (Format: hash\tHEAD)
            $output = trim($lsRemote->output());
            $commitHash = explode("\t", $output)[0];

            // 2. Check for an existing report for this repo and commit hash
            $existingReport = Report::where('repository_url', $url)
                ->where('commit_hash', $commitHash)
                ->first();

            if ($existingReport) {
                return response()->json([
                    'valid' => true,
                    'redirect_url' => route('report.show', ['uuid' => $existingReport->uuid])
                ]);
            }

            try {
                $report = Report::create([
                    'uuid' => (string) Str::uuid(),
                    'repository_url' => $url,
                    'commit_hash' => $commitHash,
                    'status' => 'pending',
                ]);
            } catch (UniqueConstraintViolationException $e) {
                // Handle race condition: if another request created the report just now
                $report = Report::where('repository_url', $url)
                    ->where('commit_hash', $commitHash)
                    ->first();
                
                if (!$report) {
                    throw $e; // Rethrow if it's a different unique constraint violation
                }

                return response()->json([
                    'valid' => true,
                    'redirect_url' => route('report.show', ['uuid' => $report->uuid])
                ]);
            }

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
