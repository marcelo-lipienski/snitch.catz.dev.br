<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'uuid',
        'repository_url',
        'commit_hash',
        'status',
        'data',
        'previous_report_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the previous report for this repository.
     */
    public function previousReport(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'previous_report_id');
    }

    /**
     * Get the full history of reports for this repository.
     */
    public function getHistory()
    {
        $history = collect([$this]);
        $current = $this;

        while ($current->previous_report_id && $previous = Report::find($current->previous_report_id)) {
            $history->push($previous);
            $current = $previous;
        }

        return $history;
    }
}
