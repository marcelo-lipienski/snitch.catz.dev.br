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
            $history = $report->getHistory();
            $previousReportData = null;
            if ($report->previous_report_id && $prev = Report::find($report->previous_report_id)) {
                $previousReportData = $this->resolveReportData($prev);
            }
            return view('report.details', compact('report', 'reportData', 'history', 'previousReportData'));
        }

        return view('report.show', compact('report'));
    }

    public function business($uuid)
    {
        $report = Report::where('uuid', $uuid)->firstOrFail();

        if ($report->status === 'completed') {
            $reportData = $this->resolveReportData($report);
            $history = $report->getHistory();
            $previousReportData = null;
            if ($report->previous_report_id && $prev = Report::find($report->previous_report_id)) {
                $previousReportData = $this->resolveReportData($prev);
            }
            return view('report.business', compact('report', 'reportData', 'history', 'previousReportData'));
        }

        return abort(404, 'Business report not found or analysis not completed.');
    }

    public function architecture($uuid)
    {
        $report = Report::where('uuid', $uuid)->firstOrFail();

        if ($report->status === 'completed') {
            $reportData = $this->resolveReportData($report);
            $markdown = $reportData['technical']['architecture_suggestion'] ?? '# No architecture suggestion available.';
            return view('report.architecture', compact('report', 'markdown'));
        }

        return abort(404, 'Architecture suggestion not found or analysis not completed.');
    }

    public function previewTechnical()
    {
        $prev = Report::firstOrCreate(
            ['uuid' => 'preview-uuid-prev'],
            [
                'status' => 'completed',
                'repository_url' => 'https://github.com/example/repo',
                'commit_hash' => 'prevhash1234567890',
                'data' => $this->getSampleJsonData(85), // Higher maintainability
            ]
        );

        $report = Report::firstOrCreate(
            ['uuid' => 'preview-uuid'],
            [
                'status' => 'completed',
                'repository_url' => 'https://github.com/example/repo',
                'commit_hash' => 'abcdef1234567890',
                'data' => $this->getSampleJsonData(78), // Current maintainability
                'previous_report_id' => $prev->id,
            ]
        );

        $reportData = $this->resolveReportData($report);
        $previousReportData = $this->resolveReportData($prev);
        $history = $report->getHistory();
        
        return view('report.details', compact('report', 'reportData', 'history', 'previousReportData'));
    }

    public function previewBusiness()
    {
        $prev = Report::firstOrCreate(
            ['uuid' => 'preview-uuid-prev'],
            [
                'status' => 'completed',
                'repository_url' => 'https://github.com/example/repo',
                'commit_hash' => 'prevhash1234567890',
                'data' => $this->getSampleJsonData(85),
            ]
        );

        $report = Report::firstOrCreate(
            ['uuid' => 'preview-uuid'],
            [
                'status' => 'completed',
                'repository_url' => 'https://github.com/example/repo',
                'commit_hash' => 'abcdef1234567890',
                'data' => $this->getSampleJsonData(78),
                'previous_report_id' => $prev->id,
            ]
        );

        $reportData = $this->resolveReportData($report);
        $previousReportData = $this->resolveReportData($prev);
        $history = $report->getHistory();

        return view('report.business', compact('report', 'reportData', 'history', 'previousReportData'));
    }

    public function previewArchitecture()
    {
        $report = Report::firstOrCreate(
            ['uuid' => 'preview-uuid'],
            [
                'status' => 'completed',
                'repository_url' => 'https://github.com/example/repo',
                'commit_hash' => 'abcdef1234567890',
                'data' => $this->getSampleJsonData(78),
            ]
        );

        $reportData = $this->resolveReportData($report);
        $markdown = $reportData['technical']['architecture_suggestion'] ?? '# No architecture suggestion available.';
        return view('report.architecture', compact('report', 'markdown'));
    }

    private function getSampleJsonData($maintainability = 78.45)
    {
        return [
            "issues" => [
                [
                    "file" => "src/Auth/Manager.php",
                    "full_path" => "/home/user/project/src/Auth/Manager.php",
                    "line" => 42,
                    "end_line" => 45,
                    "message" => "Potential SQL injection in query building. Use prepared statements instead.",
                    "severity" => "critical",
                    "rule" => "security/sql-injection",
                    "snippet" => '$query = "SELECT * FROM users WHERE id = " . $id;',
                    "snippet_start_line" => 41,
                    "path" => [
                        ["file" => "src/Auth/Manager.php", "line" => 12, "snippet" => '$id = $_GET[\'id\'];'],
                        ["file" => "src/Auth/Manager.php", "line" => 42, "snippet" => '$query = "SELECT * FROM users WHERE id = " . $id;']
                    ]
                ],
                [
                    "file" => "src/Utils/Helper.php",
                    "line" => 156,
                    "message" => "Deeply nested code detected (level 5). Consider refactoring.",
                    "severity" => "warning",
                    "rule" => "architecture/deep-nesting",
                    "snippet" => "if (\$a) { if (\$b) { if (\$c) { if (\$d) { if (\$e) { ... } } } } }"
                ]
            ],
            "stats" => [
                "files_analyzed" => 124,
                "total_lines" => 15420,
                "total_issues" => 42,
                "critical_issues" => 3,
                "error_issues" => 8,
                "warning_issues" => 25,
                "info_issues" => 6
            ],
            "maintainability_index" => $maintainability,
            "instability_index" => 32,
            "avg_halstead_volume" => 452.12,
            "avg_lcom4" => 1.4,
            "security_issue_count" => 5,
            "debt_score" => 12.5,
            "total_debt_hours" => 24.5,
            "issue_counts_by_category" => [
                "security" => 5,
                "architecture" => 12,
                "complexity" => 8,
                "style" => 17
            ],
            "duplications" => [
                [
                    "files" => ["src/Service/OrderService.php", "src/Service/InvoiceService.php"],
                    "lines" => 35
                ]
            ],
            "file_churn" => [
                "src/Auth/Manager.php" => 85,
                "src/UI/Dashboard.php" => 42,
                "src/Core/Container.php" => 12
            ],
            "hotspots" => [
                [
                    "file" => "src/Auth/Manager.php",
                    "churn" => 85,
                    "complexity" => 142,
                    "risk_score" => 92.4,
                    "risk_level" => "Critical",
                    "churn_level" => "Volatile",
                    "impact_nodes" => [
                        [
                            "file" => "src/Auth/Manager.php",
                            "line" => 42,
                            "snippet" => "public function authenticate(\$id)",
                            "variable" => "\$id",
                            "type" => "variable"
                        ]
                    ]
                ]
            ],
            "test_coverage_ratio" => 0.824,
            "line_coverage" => [
                "total_statements" => 12000,
                "covered_statements" => 9888,
                "percentage" => 82.4
            ],
            "coverage_lines" => [
                "src/Auth/Manager.php" => [1, 2, 3, 5, 6, 10, 11, 12, 40, 41, 42],
                "src/Utils/Helper.php" => [5, 6, 7, 8, 9, 20, 21]
            ],
            "complexity_distribution" => [
                "1-5" => 85,
                "6-10" => 24,
                "11-20" => 10,
                "21+" => 5
            ],
            "risk_profile" => [
                "score" => 65,
                "bug_propensity" => 45,
                "mttr_score" => 12.2,
                "onboarding_difficulty" => 75,
                "security_risk" => 80,
                "rating" => "Moderate"
            ],
            "dependency_graph" => "graph TD\n  A[Auth/Manager] --> B[Core/Container]\n  A --> C[Utils/Helper]\n  D[UI/Dashboard] --> A",
            "architecture_md" => $this->getDefaultArchitectureSuggestion()
        ];
    }

    private function buildFileTree($findings)
    {
        $tree = [];
        foreach ($findings as $path => $issues) {
            $parts = explode('/', $path);
            $current = &$tree;
            foreach ($parts as $part) {
                if (!isset($current[$part])) {
                    $current[$part] = ['_is_file' => false, '_children' => []];
                }
                $current = &$current[$part]['_children'];
            }
            // Mark as file and store its full path and issues
            $lastPart = end($parts);
            // We need to re-find the node because $current points to children
            $node = &$tree;
            foreach (array_slice($parts, 0, -1) as $part) {
                $node = &$node[$part]['_children'];
            }
            $node[$lastPart]['_is_file'] = true;
            $node[$lastPart]['_full_path'] = $path;
            $node[$lastPart]['_issue_count'] = count($issues);
        }
        return $tree;
    }

    private function resolveReportData(Report $report)
    {
        if (empty($report->data)) {
            return $this->getEmptyDataStructure();
        }

        $data = $report->data;
        $riskProfile = $data['risk_profile'] ?? [];
        $issueCounts = $data['issue_counts_by_category'] ?? [];
        
        $issues = $data['issues'] ?? [];
        $groupedFindings = [];
        foreach ($issues as $issue) {
            $file = $issue['file'] ?? 'Unknown';
            $severity = strtolower($issue['severity'] ?? 'medium');
            
            if (!isset($groupedFindings[$file])) {
                $groupedFindings[$file] = [];
            }
            
            $groupedFindings[$file][] = [
                'icon' => str_contains($issue['rule'] ?? '', 'security') ? 'security' : 
                         (str_contains($issue['rule'] ?? '', 'architecture') ? 'architecture' : 'warning'),
                'severity' => $severity === 'critical' ? 'high' : ($severity === 'warning' ? 'medium' : 'low'),
                'title' => $issue['rule'] ?? 'Code Issue',
                'description' => $issue['message'] ?? '',
                'line' => $issue['line'] ?? null,
            ];
        }

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
                'findings' => $groupedFindings,
                'file_tree' => $this->buildFileTree($groupedFindings),
                'architecture_suggestion' => $data['architecture_md'] ?? $data['architecture_suggestion'] ?? $this->getDefaultArchitectureSuggestion(),
            ],
            'business' => [
                'summary' => $this->generateSummary($data),
                'roadmap_opportunity_cost' => ($data['total_debt_hours'] ?? 0) . ' hrs',
                'governance_liability' => $riskProfile['rating'] ?? 'Unknown',
                'feature_velocity_index' => round($data['maintainability_index'] ?? 0) . '%',
                'risk_dimensions' => [
                    ['label' => 'Service Continuity Risk', 'value' => round($riskProfile['bug_propensity'] ?? 0), 'description' => 'Potential for unforced service outages'],
                    ['label' => 'Change Resistance', 'value' => round($data['instability_index'] ?? 0), 'description' => 'Structural friction limiting rapid iteration'],
                    ['label' => 'Talent Scaling Friction', 'value' => round($riskProfile['onboarding_difficulty'] ?? 0), 'description' => 'Lag time for new hires to achieve ROI'],
                    ['label' => 'Data Breach Liability', 'value' => round($riskProfile['security_risk'] ?? 0), 'description' => 'Vulnerability to financial and legal penalties'],
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

    private function getDefaultArchitectureSuggestion()
    {
        return <<<MARKDOWN
# Suggested ARCHITECTURE.md

## Overview
This document outlines the intended architectural patterns and principles for this project.

## Core Principles
- **Separation of Concerns**: Each component should have a single responsibility.
- **Dependency Inversion**: High-level modules should not depend on low-level modules.
- **Maintainability**: Code should be easy to read and evolve.

## Proposed Structure
- `app/Core`: Essential business logic and entities.
- `app/Features`: Feature-specific implementations.
- `app/Infrastructure`: External integrations (DB, API, etc).

## Technical Debt Areas
Based on current analysis, focus on reducing complexity in high-churn files.
MARKDOWN;
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
