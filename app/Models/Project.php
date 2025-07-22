<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'location',
        'type',
        'status',
        'description',
        'client_name',
        'start_date',
        'end_date',
        'pic_id',
        'dokumen_path'
    ];

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function costs()
    {
        return $this->hasMany(Cost::class);
    }

    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class);
    }

    public function hseReports()
    {
        return $this->hasMany(HseReport::class);
    }

    public function CalendarEvents()
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function documents()
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class);
    }
}
