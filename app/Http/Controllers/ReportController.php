<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function show($uuid)
    {
        $report = Report::where('uuid', $uuid)->firstOrFail();

        return view('report.show', compact('report'));
    }
}
