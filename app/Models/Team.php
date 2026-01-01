<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'description', 'team_leader_id'];

    public function leader()
    {
        return $this->belongsTo(Employee::class, 'team_leader_id');
    }

    public function members()
    {
        return $this->belongsToMany(Employee::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
