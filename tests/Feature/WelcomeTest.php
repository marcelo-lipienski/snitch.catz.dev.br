<?php

namespace Tests\Feature;

use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Carbon\Carbon;

class WelcomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_shows_latest_completed_reports()
    {
        $now = Carbon::now();

        // Create 6 completed reports with different timestamps
        for ($i = 1; $i <= 6; $i++) {
            Carbon::setTestNow($now->copy()->addMinutes($i));
            Report::create([
                'uuid' => (string) Str::uuid(),
                'repository_url' => "https://github.com/test/repo-{$i}",
                'commit_hash' => "hash-{$i}",
                'status' => 'completed',
                'data' => ['maintainability_index' => 80 + $i],
            ]);
        }

        // Create 1 pending report (should not be shown)
        Carbon::setTestNow($now->copy()->addMinutes(10));
        Report::create([
            'uuid' => (string) Str::uuid(),
            'repository_url' => "https://github.com/test/pending-repo",
            'commit_hash' => "pending-hash",
            'status' => 'pending',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Latest Public Analyses');
        
        // Should see the last 5 reports (repo-6 down to repo-2)
        for ($i = 2; $i <= 6; $i++) {
            $response->assertSee("https://github.com/test/repo-{$i}");
        }

        // Should not see the oldest completed report (repo-1)
        $response->assertDontSee("https://github.com/test/repo-1");
        
        // Should not see the pending report
        $response->assertDontSee("https://github.com/test/pending-repo");
    }

    public function test_welcome_page_does_not_show_section_if_no_reports()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Latest Public Analyses');
    }
}
