<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function show($uuid, Request $request)
    {
        $report = Report::where('uuid', $uuid)->firstOrFail();

        if ($report->status === 'completed') {
            $reportData = $this->resolveReportData($report);
            return view('report.details', compact('report', 'reportData'));
        }

        return view('report.show', compact('report'));
    }

    public function business($uuid)
    {
        $report = Report::where('uuid', $uuid)->firstOrFail();

        if ($report->status === 'completed') {
            $reportData = $this->resolveReportData($report);
            return view('report.business', compact('report', 'reportData'));
        }

        return abort(404, 'Business report not found or analysis not completed.');
    }

    private function resolveReportData(Report $report)
    {
        if (empty($report->data)) {
            return $this->getEmptyDataStructure();
        }

        $data = $report->data;
        $riskProfile = $data['risk_profile'] ?? [];
        $issueCounts = $data['issue_counts_by_category'] ?? [];
        
        // Map raw Snitch data to the view's expected structure
        return [
            'technical' => [
                'system_health' => round($data['maintainability_index'] ?? 0),
                'risk_profile' => $riskProfile['rating'] ?? 'Unknown',
                'risk_score' => $riskProfile['score'] ?? 0,
                'debt_recovery' => ($data['total_debt_hours'] ?? 0) . ' hrs',
                'maintainability_index' => round($data['maintainability_index'] ?? 0),
                'complexity_score' => round(($data['complexity_distribution']['21+'] ?? 0) * 10 + 50), // Derived
                'duplication_score' => count($data['duplications'] ?? []),
                'findings' => array_map(function($issue) {
                    $severity = strtolower($issue['severity'] ?? 'medium');
                    return [
                        'icon' => str_contains($issue['rule'] ?? '', 'security') ? 'security' : 
                                 (str_contains($issue['rule'] ?? '', 'architecture') ? 'architecture' : 'warning'),
                        'severity' => $severity === 'critical' ? 'high' : ($severity === 'warning' ? 'medium' : 'low'),
                        'title' => $issue['rule'] ?? 'Code Issue',
                        'description' => $issue['message'] ?? ''
                    ];
                }, array_slice($data['issues'] ?? [], 0, 5)),
            ],
            'business' => [
                'summary' => $this->generateSummary($data),
                'roadmap_opportunity_cost' => ($data['total_debt_hours'] ?? 0) . ' hrs',
                'governance_liability' => $riskProfile['rating'] ?? 'Unknown',
                'feature_velocity_index' => round($data['maintainability_index'] ?? 0) . '%',
                'risk_dimensions' => [
                    ['label' => 'Service Continuity Risk', 'value' => round(($riskProfile['bug_propensity'] ?? 0) * 100), 'description' => 'Potential for unforced service outages'],
                    ['label' => 'Change Resistance', 'value' => round(($data['instability_index'] ?? 0) * 100), 'description' => 'Structural friction limiting rapid iteration'],
                    ['label' => 'Talent Scaling Friction', 'value' => round(($riskProfile['onboarding_difficulty'] ?? 0) * 10), 'description' => 'Lag time for new hires to achieve ROI'],
                    ['label' => 'Data Breach Liability', 'value' => round(($riskProfile['security_risk'] ?? 0) * 100), 'description' => 'Vulnerability to financial and legal penalties'],
                ],
                'technical_interest' => [
                    ['label' => 'Architecture', 'value' => $issueCounts['architecture'] ?? 0, 'blocks' => ($issueCounts['architecture'] ?? 0) * 2],
                    ['label' => 'Clean Code', 'value' => $issueCounts['style'] ?? 0, 'blocks' => ($issueCounts['style'] ?? 0) * 2],
                    ['label' => 'Code Smell', 'value' => $issueCounts['complexity'] ?? 0, 'blocks' => ($issueCounts['complexity'] ?? 0) * 2],
                    ['label' => 'Type Safety', 'value' => $issueCounts['security'] ?? 0, 'blocks' => ($issueCounts['security'] ?? 0) * 2],
                ],
                'hotspots' => array_map(function($hotspot) {
                    return [
                        'file' => $hotspot['file'] ?? 'Unknown',
                        'score' => $hotspot['risk_score'] ?? 0,
                        'volatility' => $hotspot['churn_level'] ?? 'Stable'
                    ];
                }, array_slice($data['hotspots'] ?? [], 0, 3)),
            ]
        ];
    }

    private function generateSummary($data)
    {
        $health = round($data['maintainability_index'] ?? 0);
        $risk = $data['risk_profile']['rating'] ?? 'Unknown';
        $debt = $data['total_debt_hours'] ?? 0;

        return "The current strategic assessment of the codebase reveals a system health rating of {$health}% with a {$risk} overall risk profile. The organization is currently carrying {$debt} hours of \"Technical Interest,\" which is exerting pressure on roadmap throughput.";
    }

    private function getEmptyDataStructure()
    {
        return [
            'technical' => [
                'system_health' => 0,
                'risk_profile' => 'Unknown',
                'risk_score' => 0,
                'debt_recovery' => '0 hrs',
                'maintainability_index' => 0,
                'complexity_score' => 0,
                'duplication_score' => 0,
                'findings' => [],
            ],
            'business' => [
                'summary' => 'No analysis data available.',
                'roadmap_opportunity_cost' => '0 hrs',
                'governance_liability' => 'Unknown',
                'feature_velocity_index' => '0%',
                'risk_dimensions' => [],
                'technical_interest' => [],
                'hotspots' => [],
            ]
        ];
    }

    public function status($uuid)
    {
        $report = Report::where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'status' => $report->status,
            'is_completed' => $report->status === 'completed',
        ]);
    }
}
