<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AnalyzeRepositoryJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public Report $report)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->report->update(['status' => 'processing']);

        $uuid = $this->report->uuid;
        $url = $this->report->repository_url;
        
        $basePath = storage_path("app/reports/{$uuid}");
        $repoPath = "{$basePath}/repository";
        $reportOutputPath = "{$basePath}/snitch-report";

        File::ensureDirectoryExists($repoPath);
        File::ensureDirectoryExists($reportOutputPath);

        try {
            // 1. Clone the repository
            // -c core.hooksPath=/dev/null: ensure no hooks are executed
            // --depth 1: shallow clone
            // --no-checkout: we might want to check it out later, but clone --depth 1 performs checkout by default.
            // Using -c core.hooksPath=/dev/null is the primary defense against hooks.
            $clone = Process::env(['GIT_TERMINAL_PROMPT' => '0'])
                ->run([
                    'git', 
                    '-c', 'core.hooksPath=/dev/null',
                    'clone', 
                    '--depth', '1', 
                    '--quiet',
                    $url, 
                    $repoPath
                ]);

            if (!$clone->successful()) {
                Log::error("Failed to clone repository for report {$uuid}: " . $clone->errorOutput());
                $this->fail(new \Exception("Failed to clone repository: " . $clone->errorOutput()));
                return;
            }

            // 2. Verify the commit hash we actually got
            $getHash = Process::path($repoPath)->run([
                'git', 
                '-c', 'core.hooksPath=/dev/null',
                'rev-parse', 
                'HEAD'
            ]);
            $actualHash = trim($getHash->output());

            if ($actualHash !== $this->report->commit_hash) {
                // If the hash changed since the job was dispatched, update the report.
                // Note: This might fail if a report for $actualHash already exists due to the unique constraint.
                try {
                    $this->report->update(['commit_hash' => $actualHash]);
                } catch (\Exception $e) {
                    // If update fails (likely unique constraint), it means another report for this hash already exists.
                    // We can mark this report as "duplicate" or just fail it.
                    Log::info("Report {$uuid} for {$url} found a newer hash {$actualHash} which already has a report. Skipping.");
                    $this->report->update(['status' => 'failed']); // Or a new status like 'duplicate'
                    return;
                }
            }

            // 3. Run Snitch Docker image
            // Command: docker run --rm --user $(id -u):$(id -g) -v $(pwd):/data -v $(pwd)/snitch-report:/reports mlipienski/snitch
            $uid = posix_getuid();
            $gid = posix_getgid();

            $docker = Process::timeout(90) // 90 seconds timeout
                ->run([
                    'docker', 'run', '--rm',
                    '--user', "{$uid}:{$gid}",
                    '-v', "{$repoPath}:/data",
                    'mlipienski/snitch'
                ]);

            if (!$docker->successful()) {
                Log::error("Snitch Docker execution failed for report {$uuid}: " . $docker->errorOutput());
                $this->fail(new \Exception("Snitch analysis failed: " . $docker->errorOutput()));
                return;
            }

            // 4. Capture and store the JSON report from stdout
            $jsonReport = trim($docker->output());
            $data = json_decode($jsonReport, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("Snitch output for report {$uuid} is not valid JSON: " . json_last_error_msg());
                $this->report->update([
                    'status' => 'failed',
                    'data' => ['error' => 'Invalid JSON report received', 'raw_output' => $jsonReport]
                ]);
                return;
            }

            $this->report->update([
                'status' => 'completed',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            $this->report->update(['status' => 'failed']);
            Log::error("Error in AnalyzeRepositoryJob for report {$uuid}: " . $e->getMessage());
            throw $e;
        } finally {
            // Optional: Cleanup the cloned repo but keep the reports
            // File::deleteDirectory($repoPath);
        }
    }
}
