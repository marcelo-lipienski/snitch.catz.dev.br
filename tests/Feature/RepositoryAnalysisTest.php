<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use App\Models\Report;
use App\Jobs\AnalyzeRepositoryJob;
use Illuminate\Support\Facades\Queue;

class RepositoryAnalysisTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        File::deleteDirectory(storage_path('app/reports'));
    }

    public function test_analyze_dispatches_job(): void
    {
        Queue::fake();
        $url = 'https://github.com/laravel/laravel';
        
        Process::fake([
            "git ls-remote --exit-code {$url}" => Process::result('', '', 0),
        ]);

        $response = $this->postJson('/analyze', [
            'url' => $url
        ]);

        $response->assertStatus(200);
        
        $report = Report::first();
        Queue::assertPushed(AnalyzeRepositoryJob::class, function ($job) use ($report) {
            return $job->report->id === $report->id;
        });
    }

    public function test_analyze_requires_url(): void
    {
        $response = $this->postJson('/analyze', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['url']);
    }

    public function test_analyze_fails_for_invalid_git_repository(): void
    {
        Process::fake([
            '*' => function ($process) {
                $command = is_array($process->command) ? implode(' ', $process->command) : $process->command;
                if (str_contains($command, 'ls-remote')) {
                    return Process::result('', '', 1);
                }
                return Process::result('', '', 0);
            },
        ]);

        $response = $this->postJson('/analyze', [
            'url' => 'https://github.com/invalid/repo'
        ]);

        $response->assertStatus(422);
        $response->assertJson(['valid' => false, 'error' => 'Invalid git repository']);
    }

    public function test_analyze_handles_url_without_protocol(): void
    {
        Queue::fake();
        $inputUrl = 'github.com/marcelo-lipienski/snitch.catz.dev.br';
        $fullUrl = 'https://' . $inputUrl;

        Process::fake([
            "git ls-remote --exit-code {$fullUrl}" => Process::result('', '', 0),
        ]);

        $response = $this->postJson('/analyze', [
            'url' => $inputUrl
        ]);

        $response->assertStatus(200);
        
        $report = Report::first();
        $this->assertNotNull($report);
        $this->assertEquals($fullUrl, $report->repository_url);
        Queue::assertPushed(AnalyzeRepositoryJob::class);
    }
}
