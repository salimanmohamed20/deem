<?php

namespace App\Http\Controllers;

use App\Models\TaskAttachment;
use App\Models\CommentAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function downloadTaskAttachment(TaskAttachment $attachment): StreamedResponse
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    public function previewTaskAttachment(TaskAttachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        $mimeType = $attachment->file_type;
        
        // Only allow preview for safe file types
        $previewableTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
        ];

        if (!in_array($mimeType, $previewableTypes)) {
            return $this->downloadTaskAttachment($attachment);
        }

        return response()->file(
            Storage::disk('public')->path($attachment->file_path),
            ['Content-Type' => $mimeType]
        );
    }

    public function downloadCommentAttachment(CommentAttachment $attachment): StreamedResponse
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    public function previewCommentAttachment(CommentAttachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        $mimeType = $attachment->file_type;
        
        $previewableTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
        ];

        if (!in_array($mimeType, $previewableTypes)) {
            return $this->downloadCommentAttachment($attachment);
        }

        return response()->file(
            Storage::disk('public')->path($attachment->file_path),
            ['Content-Type' => $mimeType]
        );
    }
}
