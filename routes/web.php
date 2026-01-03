<?php

use App\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Attachment routes (protected by auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/attachments/task/{attachment}/download', [AttachmentController::class, 'downloadTaskAttachment'])
        ->name('attachments.task.download');
    Route::get('/attachments/task/{attachment}/preview', [AttachmentController::class, 'previewTaskAttachment'])
        ->name('attachments.task.preview');
    Route::get('/attachments/comment/{attachment}/download', [AttachmentController::class, 'downloadCommentAttachment'])
        ->name('attachments.comment.download');
    Route::get('/attachments/comment/{attachment}/preview', [AttachmentController::class, 'previewCommentAttachment'])
        ->name('attachments.comment.preview');
});
