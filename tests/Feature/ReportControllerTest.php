<?php

namespace Tests\Feature;

use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_serves_details_view_when_completed()
    {
        $uuid = (string) Str::uuid();
        $report = Report::create([
            'uuid' => $uuid,
            'repository_url' => 'https://github.com/test/repo',
            'commit_hash' => 'hash123',
            'status' => 'completed',
        ]);

        $response = $this->get("/report/{$uuid}");

        $response->assertStatus(200);
        $response->assertSee('Technical Deep Dive');
        $response->assertSee('System Health');
    }

    public function test_show_handles_missing_technical_key_gracefully()
    {
        $uuid = (string) Str::uuid();
        $report = Report::create([
            'uuid' => $uuid,
            'repository_url' => 'https://github.com/test/repo',
            'commit_hash' => 'hash123',
            'status' => 'completed',
            'data' => ['something' => 'else'] // Missing 'technical' key
        ]);

        $response = $this->get("/report/{$uuid}");

        $response->assertStatus(200);
        $response->assertSee('Technical Deep Dive');
        // It should fallback to dummy data for 'technical'
        $response->assertSee('System Health');
        $response->assertSee('94%'); // Dummy value
    }

    public function test_business_serves_business_view_when_completed()
    {
        $uuid = (string) Str::uuid();
        $report = Report::create([
            'uuid' => $uuid,
            'repository_url' => 'https://github.com/test/repo',
            'commit_hash' => 'hash123',
            'status' => 'completed',
        ]);

        $response = $this->get("/report/{$uuid}/business");

        $response->assertStatus(200);
        $response->assertSee('Business Insights');
        $response->assertSee('Strategic Executive Summary');
    }

    public function test_business_handles_missing_business_key_gracefully()
    {
        $uuid = (string) Str::uuid();
        $report = Report::create([
            'uuid' => $uuid,
            'repository_url' => 'https://github.com/test/repo',
            'commit_hash' => 'hash123',
            'status' => 'completed',
            'data' => ['technical' => ['system_health' => 100]] // Missing 'business' key
        ]);

        $response = $this->get("/report/{$uuid}/business");

        $response->assertStatus(200);
        $response->assertSee('Business Insights');
        // It should fallback to dummy data for 'business'
        $response->assertSee('Strategic Executive Summary');
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
