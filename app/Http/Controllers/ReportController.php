<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ReportController extends Controller
{
    public function show($uuid, Request $request)
    {
        $report = Report::where('uuid', $uuid)->firstOrFail();

        if ($report->status === 'completed') {
            $path = storage_path("app/reports/{$uuid}/snitch-report/index.html");

            if (File::exists($path)) {
                $content = File::get($path);
                $content = $this->injectLinkFixer($content, $uuid);
                
                return response($content, 200, [
                    'Content-Type' => 'text/html; charset=utf-8',
                ]);
            }
        }

        return view('report.show', compact('report'));
    }

    public function business($uuid)
    {
        $report = Report::where('uuid', $uuid)->firstOrFail();

        if ($report->status === 'completed') {
            $path = storage_path("app/reports/{$uuid}/snitch-report/business");

            if (File::exists($path)) {
                $content = File::get($path);
                $content = $this->injectLinkFixer($content, $uuid);

                return response($content, 200, [
                    'Content-Type' => 'text/html; charset=utf-8',
                ]);
            }
        }

        return abort(404, 'Business report not found or analysis not completed.');
    }

    private function injectLinkFixer($content, $uuid)
    {
        $script = <<<EOT
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deepDiveLink = document.getElementById('nav-deep-dive');
                const businessLink = document.getElementById('nav-business');
                
                if (deepDiveLink) {
                    deepDiveLink.setAttribute('href', '/report/{$uuid}');
                }
                if (businessLink) {
                    businessLink.setAttribute('href', '/report/{$uuid}/business');
                }
            });
        </script>
        EOT;

        return str_replace('</body>', $script . '</body>', $content);
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
