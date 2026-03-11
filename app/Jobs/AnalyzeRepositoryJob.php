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
            // We clone the default branch with depth 1.
            $clone = Process::env(['GIT_TERMINAL_PROMPT' => '0'])
                ->run(['git', 'clone', '--depth', '1', $url, $repoPath]);

            if (!$clone->successful()) {
                Log::error("Failed to clone repository for report {$uuid}: " . $clone->errorOutput());
                $this->fail(new \Exception("Failed to clone repository: " . $clone->errorOutput()));
                return;
            }

            // 2. Verify the commit hash we actually got
            $getHash = Process::path($repoPath)->run(['git', 'rev-parse', 'HEAD']);
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
                    '-v', "{$reportOutputPath}:/reports",
                    'mlipienski/snitch'
                ]);

            if (!$docker->successful()) {
                Log::error("Snitch Docker execution failed for report {$uuid}: " . $docker->errorOutput());
                $this->fail(new \Exception("Snitch analysis failed: " . $docker->errorOutput()));
                return;
            }

            $this->report->update(['status' => 'completed']);

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
