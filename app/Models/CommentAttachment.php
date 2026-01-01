<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentAttachment extends Model
{
    protected $fillable = [
        'task_comment_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
    ];

    public function comment()
    {
        return $this->belongsTo(TaskComment::class, 'task_comment_id');
    }
}
