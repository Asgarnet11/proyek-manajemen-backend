<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'pic_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function approvedQualityChecks()
    {
        return $this->hasMany(QualityCheck::class, 'approved_by');
    }

    public function hseReports()
    {
        return $this->hasMany(HseReport::class, 'reported_by');
    }

    public function createdCalendarEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'created_by');
    }

    public function uploadedDocuments()
    {
        return $this->hasMany(ProjectDocument::class, 'uploaded_by');
    }
}
