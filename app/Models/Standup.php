<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Standup extends Model
{
    protected $fillable = ['employee_id', 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function entries()
    {
        return $this->hasMany(StandupEntry::class);
    }
}
