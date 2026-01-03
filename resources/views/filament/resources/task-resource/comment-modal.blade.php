@php
function formatFileSizeForDisplay($bytes) {
    if (!$bytes) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}
@endphp

<div class="comment-modal">
    <style>
        .comment-modal {
            padding: 4px;
        }
        .comment-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            margin-bottom: 20px;
        }
        .dark .comment-header {
            border-bottom-color: rgba(255,255,255,0.08);
        }
        .comment-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        .comment-meta h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 4px 0;
        }
        .dark .comment-meta h3 {
            color: #f9fafb;
        }
        .comment-meta p {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }
        .comment-meta .edited {
            font-size: 11px;
            color: #9ca3af;
            margin-left: 6px;
        }
        .comment-content {
            background: #f9fafb;
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.7;
            color: #374151;
        }
        .dark .comment-content {
            background: rgba(255,255,255,0.04);
            color: #e5e7eb;
        }
        .comment-content p {
            margin: 0 0 12px 0;
        }
        .comment-content p:last-child {
            margin-bottom: 0;
        }
        .attachments-section {
            border-top: 1px solid rgba(0,0,0,0.06);
            padding-top: 16px;
        }
        .dark .attachments-section {
            border-top-color: rgba(255,255,255,0.08);
        }
        .attachments-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 14px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .dark .attachments-title {
            color: #d1d5db;
        }
        .attachments-title svg {
            width: 16px;
            height: 16px;
            opacity: 0.7;
        }
        .attachments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
        }
        .attachment-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .dark .attachment-card {
            background: #1f2937;
            border-color: rgba(255,255,255,0.1);
        }
        .attachment-card:hover {
            border-color: #6366f1;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
            transform: translateY(-2px);
        }
        .attachment-preview {
            width: 100%;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            position: relative;
            overflow: hidden;
        }
        .dark .attachment-preview {
            background: rgba(255,255,255,0.05);
        }
        .attachment-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .attachment-preview.pdf { background: linear-gradient(135deg, #fef2f2, #fee2e2); }
        .attachment-preview.word { background: linear-gradient(135deg, #eff6ff, #dbeafe); }
        .attachment-preview.excel { background: linear-gradient(135deg, #f0fdf4, #dcfce7); }
        .attachment-preview.zip { background: linear-gradient(135deg, #fefce8, #fef9c3); }
        .dark .attachment-preview.pdf { background: rgba(239, 68, 68, 0.1); }
        .dark .attachment-preview.word { background: rgba(59, 130, 246, 0.1); }
        .dark .attachment-preview.excel { background: rgba(34, 197, 94, 0.1); }
        .dark .attachment-preview.zip { background: rgba(234, 179, 8, 0.1); }
        .attachment-preview svg {
            width: 32px;
            height: 32px;
        }
        .attachment-preview.pdf svg { color: #dc2626; }
        .attachment-preview.word svg { color: #2563eb; }
        .attachment-preview.excel svg { color: #16a34a; }
        .attachment-preview.zip svg { color: #ca8a04; }
        .dark .attachment-preview.pdf svg { color: #f87171; }
        .dark .attachment-preview.word svg { color: #60a5fa; }
        .dark .attachment-preview.excel svg { color: #4ade80; }
        .dark .attachment-preview.zip svg { color: #facc15; }
        .attachment-info {
            padding: 10px 12px;
        }
        .attachment-name {
            font-size: 12px;
            font-weight: 500;
            color: #374151;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }
        .dark .attachment-name {
            color: #e5e7eb;
        }
        .attachment-size {
            font-size: 11px;
            color: #9ca3af;
        }
        .attachment-actions {
            display: flex;
            gap: 6px;
            padding: 0 12px 10px;
        }
        .attachment-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .attachment-btn svg {
            width: 12px;
            height: 12px;
        }
        .attachment-btn.preview {
            background: #eff6ff;
            color: #2563eb;
        }
        .attachment-btn.preview:hover {
            background: #dbeafe;
        }
        .attachment-btn.download {
            background: #f0fdf4;
            color: #16a34a;
        }
        .attachment-btn.download:hover {
            background: #dcfce7;
        }
        .dark .attachment-btn.preview {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }
        .dark .attachment-btn.preview:hover {
            background: rgba(59, 130, 246, 0.25);
        }
        .dark .attachment-btn.download {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
        }
        .dark .attachment-btn.download:hover {
            background: rgba(34, 197, 94, 0.25);
        }
    </style>

    {{-- Header --}}
    <div class="comment-header">
        <div class="comment-avatar">
            {{ strtoupper(substr($comment->author?->user?->name ?? 'U', 0, 2)) }}
        </div>
        <div class="comment-meta">
            <h3>{{ $comment->author?->user?->name ?? 'Unknown' }}</h3>
            <p>
                {{ $comment->created_at->format('M d, Y \a\t g:i A') }}
                @if($comment->updated_at->gt($comment->created_at))
                    <span class="edited">(edited)</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Content --}}
    <div class="comment-content">
        {!! $comment->comment !!}
    </div>

    {{-- Attachments --}}
    @if($attachments->count() > 0)
        <div class="attachments-section">
            <div class="attachments-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                </svg>
                {{ $attachments->count() }} {{ Str::plural('Attachment', $attachments->count()) }}
            </div>
            
            <div class="attachments-grid">
                @foreach($attachments as $attachment)
                    @php
                        $isImage = str_starts_with($attachment->file_type ?? '', 'image/');
                        $isPdf = str_contains($attachment->file_type ?? '', 'pdf');
                        $isWord = str_contains($attachment->file_type ?? '', 'word');
                        $isExcel = str_contains($attachment->file_type ?? '', 'excel') || str_contains($attachment->file_type ?? '', 'spreadsheet');
                        $isZip = str_contains($attachment->file_type ?? '', 'zip');
                        $isPreviewable = in_array($attachment->file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf']);
                        
                        $previewClass = $isPdf ? 'pdf' : ($isWord ? 'word' : ($isExcel ? 'excel' : ($isZip ? 'zip' : '')));
                    @endphp
                    
                    <div class="attachment-card">
                        <div class="attachment-preview {{ $previewClass }}">
                            @if($isImage)
                                <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}">
                            @elseif($isPdf)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            @elseif($isWord)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            @elseif($isExcel)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                                </svg>
                            @elseif($isZip)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            @endif
                        </div>
                        
                        <div class="attachment-info">
                            <div class="attachment-name" title="{{ $attachment->file_name }}">
                                {{ $attachment->file_name }}
                            </div>
                            <div class="attachment-size">
                                {{ formatFileSizeForDisplay($attachment->file_size) }}
                            </div>
                        </div>
                        
                        <div class="attachment-actions">
                            @if($isPreviewable)
                                <a href="{{ route('attachments.comment.preview', $attachment) }}" target="_blank" class="attachment-btn preview">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    View
                                </a>
                            @endif
                            <a href="{{ route('attachments.comment.download', $attachment) }}" class="attachment-btn download">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
