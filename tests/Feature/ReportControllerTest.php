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

    public function test_show_maps_snitch_data_correctly()
    {
        $uuid = (string) Str::uuid();
        $report = Report::create([
            'uuid' => $uuid,
            'repository_url' => 'https://github.com/test/repo',
            'commit_hash' => 'hash123',
            'status' => 'completed',
            'data' => [
                'maintainability_index' => 85.5,
                'risk_profile' => 'Critical',
                'total_debt_hours' => 120,
                'issues' => [
                    ['title' => 'Issue 1', 'severity' => 'High', 'description' => 'Desc 1']
                ]
            ]
        ]);

        $response = $this->get("/report/{$uuid}");

        $response->assertStatus(200);
        $response->assertSee('86%'); // Rounded 85.5
        $response->assertSee('Critical');
        $response->assertSee('120 hrs');
        $response->assertSee('Issue 1');
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

    public function test_business_maps_snitch_data_correctly()
    {
        $uuid = (string) Str::uuid();
        $report = Report::create([
            'uuid' => $uuid,
            'repository_url' => 'https://github.com/test/repo',
            'commit_hash' => 'hash123',
            'status' => 'completed',
            'data' => [
                'maintainability_index' => 85.5,
                'risk_profile' => 'Critical',
                'total_debt_hours' => 120,
                'hotspots' => [
                    ['file' => 'App/Model.php', 'score' => 99]
                ]
            ]
        ]);

        $response = $this->get("/report/{$uuid}/business");

        $response->assertStatus(200);
        $response->assertSee('86%');
        $response->assertSee('120 hrs');
        $response->assertSee('App/Model.php');
        $response->assertSee('The current strategic assessment of the codebase reveals a system health rating of 86%');
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
