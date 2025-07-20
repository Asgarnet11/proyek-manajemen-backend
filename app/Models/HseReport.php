<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'report_type',
        'description',
        'findings',
        'corrective_action',
        'documentation_path',
        'reported_by',
        'report_date'

    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
