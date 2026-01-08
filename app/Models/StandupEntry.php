<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandupEntry extends Model
{
    protected $fillable = [
        'standup_id',
        'project_id',
        'task_id',
        'what_i_did',
        'what_i_will_do',
        'blockers',
        'time_spent',
    ];

    public function standup()
    {
        return $this->belongsTo(Standup::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
