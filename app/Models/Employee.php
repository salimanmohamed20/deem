<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'job_title_id',
        'phone',
        'hire_date',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class)->withPivot('assigned_at');
    }

    public function taskAttachments()
    {
        return $this->hasMany(TaskAttachment::class, 'uploaded_by');
    }

    public function taskComments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function standups()
    {
        return $this->hasMany(Standup::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
