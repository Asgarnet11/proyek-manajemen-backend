<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'description',
        'amount',
        'type',
        'status',
        'vendor_name',
        'invoice_path'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
