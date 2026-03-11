<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class RepositoryAnalysisTest extends TestCase
{
    use WithoutMiddleware;

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

    public function test_analyze_fails_for_repository_without_php_files(): void
    {
        $url = 'https://github.com/user/js-only-repo';
        Process::fake([
            '*' => function ($process) {
                $command = is_array($process->command) ? implode(' ', $process->command) : $process->command;
                if (str_contains($command, 'ls-tree')) {
                    return Process::result("README.md\npackage.json", '', 0);
                }
                return Process::result('', '', 0);
            },
        ]);

        $response = $this->postJson('/analyze', [
            'url' => $url
        ]);

        $response->assertStatus(422);
        $response->assertJson(['valid' => false, 'error' => 'No PHP files found']);
    }

    public function test_analyze_succeeds_for_repository_with_php_files(): void
    {
        $url = 'https://github.com/laravel/laravel';
        Process::fake([
            '*' => function ($process) {
                $command = is_array($process->command) ? implode(' ', $process->command) : $process->command;
                if (str_contains($command, 'ls-tree')) {
                    return Process::result("index.php\nsrc/App.php\nREADME.md", '', 0);
                }
                return Process::result('', '', 0);
            },
        ]);

        $response = $this->postJson('/analyze', [
            'url' => $url
        ]);

        $response->assertStatus(200);
        $response->assertJson(['valid' => true]);
    }
}
