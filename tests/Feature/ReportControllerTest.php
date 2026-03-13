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
                'maintainability_index' => 78.45,
                'total_debt_hours' => 24.5,
                'risk_profile' => [
                    'rating' => 'Moderate',
                    'score' => 65
                ],
                'issues' => [
                    [
                        'rule' => 'security/sql-injection',
                        'severity' => 'critical',
                        'message' => 'Potential SQL injection'
                    ]
                ],
                'complexity_distribution' => [
                    '21+' => 5
                ],
                'duplications' => [
                    ['lines' => 35]
                ]
            ]
        ]);

        $response = $this->get("/report/{$uuid}");

        $response->assertStatus(200);
        $response->assertSee('78%');
        $response->assertSee('Moderate');
        $response->assertSee('24.5 hrs');
        $response->assertSee('security/sql-injection');
        $response->assertSee('Potential SQL injection');
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
                'maintainability_index' => 78.45,
                'total_debt_hours' => 24.5,
                'risk_profile' => [
                    'rating' => 'Moderate',
                    'bug_propensity' => 0.45,
                    'onboarding_difficulty' => 7.5,
                    'security_risk' => 0.8
                ],
                'instability_index' => 0.32,
                'issue_counts_by_category' => [
                    'architecture' => 12,
                    'style' => 17,
                    'complexity' => 8,
                    'security' => 5
                ],
                'hotspots' => [
                    [
                        'file' => 'src/Auth/Manager.php',
                        'risk_score' => 92.4,
                        'churn_level' => 'Volatile'
                    ]
                ]
            ]
        ]);

        $response = $this->get("/report/{$uuid}/business");

        $response->assertStatus(200);
        $response->assertSee('78%');
        $response->assertSee('24.5 hrs');
        $response->assertSee('src/Auth/Manager.php');
        $response->assertSee('Volatile');
        $response->assertSee('The current strategic assessment of the codebase reveals a system health rating of 78%');
        
        // Check risk dimensions values
        $response->assertSee('45%'); // Bug propensity
        $response->assertSee('32%'); // Instability
        $response->assertSee('75%'); // Onboarding difficulty
        $response->assertSee('80%'); // Security risk
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
