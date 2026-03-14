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
use Illuminate\Support\Str;

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
        $hash = 'a150865ca34a0499ee78bef25494a5d80e4f6933';
        
        Process::fake([
            '*' => function ($process) use ($hash) {
                if (str_contains(implode(' ', $process->command), 'ls-remote')) {
                    return Process::result("{$hash}\tHEAD", '', 0);
                }
                return Process::result('', '', 0);
            },
        ]);

        $response = $this->postJson('/analyze', [
            'url' => $url
        ]);

        $response->assertStatus(200);
        
        $report = Report::first();
        $this->assertEquals($hash, $report->commit_hash);
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
                if (str_contains(implode(' ', $process->command), 'ls-remote')) {
                    return Process::result('', '', 1);
                }
                return Process::result('', '', 0);
            },
        ]);

        $response = $this->postJson('/analyze', [
            'url' => 'https://github.com/invalid/repo'
        ]);

        $response->assertStatus(422);
        $response->assertJson(['valid' => false, 'error' => 'Invalid git repository or branch']);
    }

    public function test_analyze_handles_url_without_protocol(): void
    {
        Queue::fake();
        $inputUrl = 'github.com/marcelo-lipienski/snitch.catz.dev.br';
        $fullUrl = 'https://' . $inputUrl;
        $hash = 'a150865ca34a0499ee78bef25494a5d80e4f6933';

        Process::fake([
            '*' => function ($process) use ($hash) {
                if (str_contains(implode(' ', $process->command), 'ls-remote')) {
                    return Process::result("{$hash}\tHEAD", '', 0);
                }
                return Process::result('', '', 0);
            },
        ]);

        $response = $this->postJson('/analyze', [
            'url' => $inputUrl
        ]);

        $response->assertStatus(200);
        
        $report = Report::first();
        $this->assertNotNull($report);
        $this->assertEquals($fullUrl, $report->repository_url);
        $this->assertEquals($hash, $report->commit_hash);
        Queue::assertPushed(AnalyzeRepositoryJob::class);
    }

    public function test_analyze_redirects_to_existing_report_for_same_hash(): void
    {
        Queue::fake();
        $url = 'https://github.com/laravel/laravel';
        $hash = 'a150865ca34a0499ee78bef25494a5d80e4f6933';
        
        $existingReport = Report::create([
            'uuid' => (string) Str::uuid(),
            'repository_url' => $url,
            'commit_hash' => $hash,
            'status' => 'completed',
        ]);

        Process::fake([
            '*' => function ($process) use ($hash) {
                if (str_contains(implode(' ', $process->command), 'ls-remote')) {
                    return Process::result("{$hash}\tHEAD", '', 0);
                }
                return Process::result('', '', 0);
            },
        ]);

        $response = $this->postJson('/analyze', [
            'url' => $url
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid' => true,
            'redirect_url' => route('report.show', ['uuid' => $existingReport->uuid])
        ]);

        // Should not dispatch a new job
        Queue::assertNothingPushed();
        // Should not create a new report
        $this->assertEquals(1, Report::count());
    }

    public function test_analyze_runs_new_analysis_for_new_hash(): void
    {
        Queue::fake();
        $url = 'https://github.com/laravel/laravel';
        $oldHash = 'old-hash';
        $newHash = 'new-hash';
        
        Report::create([
            'uuid' => (string) Str::uuid(),
            'repository_url' => $url,
            'commit_hash' => $oldHash,
            'status' => 'completed',
        ]);

        Process::fake([
            '*' => function ($process) use ($newHash) {
                if (str_contains(implode(' ', $process->command), 'ls-remote')) {
                    return Process::result("{$newHash}\tHEAD", '', 0);
                }
                return Process::result('', '', 0);
            },
        ]);

        $response = $this->postJson('/analyze', [
            'url' => $url
        ]);

        $response->assertStatus(200);
        
        $newReport = Report::where('commit_hash', $newHash)->first();
        $this->assertNotNull($newReport);
        $response->assertJson([
            'valid' => true,
            'redirect_url' => route('report.show', ['uuid' => $newReport->uuid])
        ]);

        Queue::assertPushed(AnalyzeRepositoryJob::class, function ($job) use ($newReport) {
            return $job->report->id === $newReport->id;
        });
    }

    public function test_analyze_links_to_previous_report(): void
    {
        Queue::fake();
        $url = 'https://github.com/laravel/laravel';
        $oldHash = 'old-hash';
        $newHash = 'new-hash';
        
        $oldReport = Report::create([
            'uuid' => (string) Str::uuid(),
            'repository_url' => $url,
            'commit_hash' => $oldHash,
            'status' => 'completed',
        ]);

        Process::fake([
            '*' => function ($process) use ($newHash) {
                if (str_contains(implode(' ', $process->command), 'ls-remote')) {
                    return Process::result("{$newHash}\tHEAD", '', 0);
                }
                return Process::result('', '', 0);
            },
        ]);

        $this->postJson('/analyze', ['url' => $url]);

        $newReport = Report::where('commit_hash', $newHash)->first();
        $this->assertNotNull($newReport);
        $this->assertEquals($oldReport->id, $newReport->previous_report_id);
    }
}
