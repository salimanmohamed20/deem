<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'project_manager_id',
        'status',
        'start_date',
        'end_date',
    ];

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'project_manager_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function standupEntries()
    {
        return $this->hasMany(StandupEntry::class);
    }
}
