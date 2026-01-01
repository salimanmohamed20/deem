<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class TaskComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'task_id',
        'employee_id',
        'comment',
        'mentioned_employees',
    ];

    protected $casts = [
        'mentioned_employees' => 'array',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function author()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function attachments()
    {
        return $this->hasMany(CommentAttachment::class);
    }
}
