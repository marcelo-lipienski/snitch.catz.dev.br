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
                return response()->file($path, [
                    'Content-Type' => 'text/html',
                ]);
            }
        }

        return view('report.show', compact('report'));
    }

    public function business($uuid)
    {
        $report = Report::where('uuid', $uuid)->firstOrFail();

        if ($report->status === 'completed') {
            $path = storage_path("app/reports/{$uuid}/snitch-report/business.html");

            if (File::exists($path)) {
                return response()->file($path, [
                    'Content-Type' => 'text/html',
                ]);
            }
        }

        return abort(404, 'Business report not found or analysis not completed.');
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
