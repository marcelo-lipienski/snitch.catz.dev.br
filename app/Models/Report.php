<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'uuid',
        'repository_url',
        'commit_hash',
        'status',
    ];
}
