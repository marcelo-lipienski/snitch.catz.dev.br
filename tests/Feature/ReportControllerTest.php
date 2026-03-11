<?php

namespace Tests\Feature;

use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_serves_index_html_when_completed()
    {
        $uuid = (string) Str::uuid();
        $report = Report::create([
            'uuid' => $uuid,
            'repository_url' => 'https://github.com/test/repo',
            'commit_hash' => 'hash123',
            'status' => 'completed',
        ]);

        $path = storage_path("app/reports/{$uuid}/snitch-report");
        File::ensureDirectoryExists($path);
        File::put("{$path}/index.html", "<html><body>Index</body></html>");

        $response = $this->get("/report/{$uuid}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=utf-8');
        $response->assertSee('/report/' . $uuid);
        $response->assertSee('nav-deep-dive');
        
        File::deleteDirectory(storage_path("app/reports/{$uuid}"));
    }

    public function test_business_serves_business_html_when_completed()
    {
        $uuid = (string) Str::uuid();
        $report = Report::create([
            'uuid' => $uuid,
            'repository_url' => 'https://github.com/test/repo',
            'commit_hash' => 'hash123',
            'status' => 'completed',
        ]);

        $path = storage_path("app/reports/{$uuid}/snitch-report");
        File::ensureDirectoryExists($path);
        File::put("{$path}/business.html", "<html><body>Business</body></html>");

        $response = $this->get("/report/{$uuid}/business");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=utf-8');
        $response->assertSee('/report/' . $uuid . '/business');
        $response->assertSee('nav-business');
        
        File::deleteDirectory(storage_path("app/reports/{$uuid}"));
    }

    public function test_business_returns_404_when_not_completed()
    {
        $uuid = (string) Str::uuid();
        $report = Report::create([
            'uuid' => $uuid,
            'repository_url' => 'https://github.com/test/repo',
            'commit_hash' => 'hash123',
            'status' => 'pending',
        ]);

        $response = $this->get("/report/{$uuid}/business");

        $response->assertStatus(404);
    }
}
