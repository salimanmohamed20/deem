<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    protected $fillable = [
        'task_id',
        'uploaded_by',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader()
    {
        return $this->belongsTo(Employee::class, 'uploaded_by');
    }
}
