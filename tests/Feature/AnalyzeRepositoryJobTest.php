<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeRepositoryJob;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Tests\TestCase;

class AnalyzeRepositoryJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        File::deleteDirectory(storage_path('app/reports'));
    }

    public function test_job_captures_json_output_from_stdout(): void
    {
        $report = Report::create([
            'uuid' => (string) Str::uuid(),
            'repository_url' => 'https://github.com/laravel/laravel',
            'commit_hash' => 'fake-hash',
            'status' => 'pending',
        ]);

        $mockData = [
            'total_issues' => 10,
            'files' => [
                ['name' => 'test.php', 'issues' => 2]
            ]
        ];

        Process::fake([
            '*' => function ($process) use ($mockData) {
                $command = implode(' ', $process->command);
                if (str_contains($command, 'rev-parse')) {
                    return Process::result('fake-hash', '', 0);
                }
                if (str_contains($command, 'docker run')) {
                    return Process::result(json_encode($mockData), '', 0);
                }
                return Process::result('', '', 0);
            },
        ]);

        $job = new AnalyzeRepositoryJob($report);
        $job->handle();

        $report->refresh();

        $this->assertEquals('completed', $report->status);
        $this->assertEquals($mockData, $report->data);
    }

    public function test_job_handles_invalid_json_output(): void
    {
        $report = Report::create([
            'uuid' => (string) Str::uuid(),
            'repository_url' => 'https://github.com/laravel/laravel',
            'commit_hash' => 'fake-hash',
            'status' => 'pending',
        ]);

        Process::fake([
            '*' => function ($process) {
                $command = implode(' ', $process->command);
                if (str_contains($command, 'rev-parse')) {
                    return Process::result('fake-hash', '', 0);
                }
                if (str_contains($command, 'docker run')) {
                    return Process::result('invalid-json', '', 0);
                }
                return Process::result('', '', 0);
            },
        ]);

        $job = new AnalyzeRepositoryJob($report);
        $job->handle();

        $report->refresh();

        $this->assertEquals('failed', $report->status);
        $this->assertArrayHasKey('error', $report->data);
        $this->assertEquals('invalid-json', $report->data['raw_output']);
    }
}
