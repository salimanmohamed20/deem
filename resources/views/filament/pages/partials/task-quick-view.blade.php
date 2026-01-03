@php
function formatFileSizeForQuickView($bytes) {
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

<div class="task-quick-view">
    @if($task)
        <style>
            .task-quick-view {
                padding: 8px 0;
            }
            .tqv-section {
                margin-bottom: 20px;
            }
            .tqv-section:last-child {
                margin-bottom: 0;
            }
            .tqv-label {
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #6b7280;
                margin-bottom: 6px;
            }
            .dark .tqv-label {
                color: #9ca3af;
            }
            .tqv-value {
                font-size: 14px;
                color: #1f2937;
            }
            .dark .tqv-value {
                color: #f3f4f6;
            }
            .tqv-badges {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }
            .tqv-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 500;
            }
            .tqv-badge-status {
                background: #dbeafe;
                color: #1d4ed8;
            }
            .tqv-badge-status.to_do {
                background: #fef3c7;
                color: #b45309;
            }
            .tqv-badge-status.in_progress {
                background: #dbeafe;
                color: #1d4ed8;
            }
            .tqv-badge-status.done {
                background: #dcfce7;
                color: #15803d;
            }
            .tqv-badge-priority {
                background: #f3f4f6;
                color: #374151;
            }
            .tqv-badge-priority.high {
                background: #fee2e2;
                color: #dc2626;
            }
            .tqv-badge-priority.medium {
                background: #fef3c7;
                color: #d97706;
            }
            .tqv-badge-priority.low {
                background: #dcfce7;
                color: #16a34a;
            }
            .dark .tqv-badge-status.to_do {
                background: rgba(251, 191, 36, 0.2);
                color: #fbbf24;
            }
            .dark .tqv-badge-status.in_progress {
                background: rgba(59, 130, 246, 0.2);
                color: #60a5fa;
            }
            .dark .tqv-badge-status.done {
                background: rgba(34, 197, 94, 0.2);
                color: #4ade80;
            }
            .dark .tqv-badge-priority.high {
                background: rgba(239, 68, 68, 0.2);
                color: #f87171;
            }
            .dark .tqv-badge-priority.medium {
                background: rgba(245, 158, 11, 0.2);
                color: #fbbf24;
            }
            .dark .tqv-badge-priority.low {
                background: rgba(34, 197, 94, 0.2);
                color: #4ade80;
            }
            .tqv-project {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 14px;
                background: #f3f4f6;
                border-radius: 8px;
                font-size: 13px;
                color: #374151;
            }
            .dark .tqv-project {
                background: rgba(255, 255, 255, 0.08);
                color: #d1d5db;
            }
            .tqv-project svg {
                width: 16px;
                height: 16px;
                opacity: 0.6;
            }
            .tqv-description {
                padding: 14px;
                background: #f9fafb;
                border-radius: 10px;
                font-size: 14px;
                line-height: 1.6;
                color: #374151;
            }
            .dark .tqv-description {
                background: rgba(255, 255, 255, 0.04);
                color: #d1d5db;
            }
            .tqv-description:empty::before {
                content: 'No description provided';
                color: #9ca3af;
                font-style: italic;
            }
            .tqv-assignees {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            .tqv-assignee {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 6px 12px 6px 6px;
                background: #f3f4f6;
                border-radius: 20px;
                font-size: 13px;
            }
            .dark .tqv-assignee {
                background: rgba(255, 255, 255, 0.08);
            }
            .tqv-assignee-avatar {
                width: 26px;
                height: 26px;
                border-radius: 50%;
                background: linear-gradient(135deg, #8b5cf6, #6366f1);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 10px;
                font-weight: 600;
                color: white;
            }
            .tqv-meta {
                display: flex;
                gap: 20px;
                padding: 14px;
                background: #f9fafb;
                border-radius: 10px;
            }
            .dark .tqv-meta {
                background: rgba(255, 255, 255, 0.04);
            }
            .tqv-meta-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                color: #6b7280;
            }
            .dark .tqv-meta-item {
                color: #9ca3af;
            }
            .tqv-meta-item svg {
                width: 16px;
                height: 16px;
            }
            .tqv-meta-item.overdue {
                color: #dc2626;
            }
            .tqv-comments {
                max-height: 200px;
                overflow-y: auto;
            }
            .tqv-comment {
                padding: 12px;
                background: #f9fafb;
                border-radius: 10px;
                margin-bottom: 10px;
            }
            .dark .tqv-comment {
                background: rgba(255, 255, 255, 0.04);
            }
            .tqv-comment:last-child {
                margin-bottom: 0;
            }
            .tqv-comment-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 6px;
            }
            .tqv-comment-author {
                font-weight: 600;
                font-size: 13px;
                color: #374151;
            }
            .dark .tqv-comment-author {
                color: #f3f4f6;
            }
            .tqv-comment-date {
                font-size: 11px;
                color: #9ca3af;
            }
            .tqv-comment-body {
                font-size: 13px;
                color: #4b5563;
                line-height: 1.5;
            }
            .dark .tqv-comment-body {
                color: #d1d5db;
            }
            .tqv-divider {
                height: 1px;
                background: #e5e7eb;
                margin: 16px 0;
            }
            .dark .tqv-divider {
                background: rgba(255, 255, 255, 0.1);
            }
            .tqv-attachments {
                display: flex;
                flex-direction: column;
                gap: 8px;
                max-height: 200px;
                overflow-y: auto;
            }
            .tqv-attachment {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 12px;
                background: #f9fafb;
                border-radius: 10px;
                border: 1px solid #e5e7eb;
            }
            .dark .tqv-attachment {
                background: rgba(255, 255, 255, 0.04);
                border-color: rgba(255, 255, 255, 0.1);
            }
            .tqv-attachment-preview {
                width: 40px;
                height: 40px;
                border-radius: 8px;
                overflow: hidden;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .tqv-attachment-preview img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .tqv-attachment-preview.file-icon {
                background: #f3f4f6;
            }
            .dark .tqv-attachment-preview.file-icon {
                background: rgba(255, 255, 255, 0.08);
            }
            .tqv-attachment-preview svg {
                width: 20px;
                height: 20px;
                color: #6b7280;
            }
            .dark .tqv-attachment-preview svg {
                color: #9ca3af;
            }
            .tqv-attachment-info {
                flex: 1;
                min-width: 0;
            }
            .tqv-attachment-name {
                font-size: 13px;
                font-weight: 500;
                color: #374151;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .dark .tqv-attachment-name {
                color: #f3f4f6;
            }
            .tqv-attachment-size {
                font-size: 11px;
                color: #9ca3af;
            }
            .tqv-attachment-actions {
                display: flex;
                gap: 6px;
            }
            .tqv-attachment-btn {
                width: 28px;
                height: 28px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.15s;
            }
            .tqv-attachment-btn svg {
                width: 14px;
                height: 14px;
            }
            .tqv-attachment-btn.preview {
                background: rgba(59, 130, 246, 0.1);
                color: #3b82f6;
            }
            .tqv-attachment-btn.preview:hover {
                background: rgba(59, 130, 246, 0.2);
            }
            .tqv-attachment-btn.download {
                background: rgba(34, 197, 94, 0.1);
                color: #22c55e;
            }
            .tqv-attachment-btn.download:hover {
                background: rgba(34, 197, 94, 0.2);
            }
        </style>

        {{-- Project --}}
        <div class="tqv-section">
            <div class="tqv-project">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3.75 3A1.75 1.75 0 0 0 2 4.75v10.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0 0 18 15.25v-8.5A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75Z" clip-rule="evenodd" />
                </svg>
                {{ $task->project->name }}
            </div>
        </div>

        {{-- Status & Priority --}}
        <div class="tqv-section">
            <div class="tqv-badges">
                <span class="tqv-badge tqv-badge-status {{ $task->status }}">
                    @if($task->status === 'to_do')
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                            <circle cx="10" cy="10" r="6" fill="none" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        To Do
                    @elseif($task->status === 'in_progress')
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/>
                        </svg>
                        In Progress
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        Done
                    @endif
                </span>
                <span class="tqv-badge tqv-badge-priority {{ $task->priority }}">
                    {{ ucfirst($task->priority) }} Priority
                </span>
            </div>
        </div>

        {{-- Description --}}
        <div class="tqv-section">
            <div class="tqv-label">Description</div>
            <div class="tqv-description">{!! $task->description ?: '' !!}</div>
        </div>

        {{-- Assignees --}}
        @if($task->assignees->count() > 0)
            <div class="tqv-section">
                <div class="tqv-label">Assigned To</div>
                <div class="tqv-assignees">
                    @foreach($task->assignees as $assignee)
                        <div class="tqv-assignee">
                            <span class="tqv-assignee-avatar">
                                {{ strtoupper(substr($assignee->user->name ?? 'U', 0, 2)) }}
                            </span>
                            {{ $assignee->user->name ?? 'Unknown' }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Meta Info --}}
        <div class="tqv-section">
            <div class="tqv-meta">
                @if($task->deadline)
                    @php
                        $deadline = \Carbon\Carbon::parse($task->deadline);
                        $isOverdue = $deadline->isPast() && $task->status !== 'done';
                    @endphp
                    <div class="tqv-meta-item {{ $isOverdue ? 'overdue' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd"/>
                        </svg>
                        {{ $isOverdue ? 'Overdue: ' : 'Due: ' }}{{ $deadline->format('M j, Y') }}
                    </div>
                @endif
                <div class="tqv-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/>
                    </svg>
                    Created {{ $task->created_at->diffForHumans() }}
                </div>
            </div>
        </div>

        {{-- Attachments --}}
        @if($task->attachments->count() > 0)
            <div class="tqv-divider"></div>
            <div class="tqv-section">
                <div class="tqv-label">Attachments ({{ $task->attachments->count() }})</div>
                <div class="tqv-attachments">
                    @foreach($task->attachments as $attachment)
                        <div class="tqv-attachment">
                            <div class="tqv-attachment-preview {{ str_starts_with($attachment->file_type ?? '', 'image/') ? '' : 'file-icon' }}">
                                @if(str_starts_with($attachment->file_type ?? '', 'image/'))
                                    <img 
                                        src="{{ asset('storage/' . $attachment->file_path) }}" 
                                        alt="{{ $attachment->file_name }}"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                                    >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none; width: 20px; height: 20px; color: #9ca3af;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                @elseif(str_contains($attachment->file_type ?? '', 'pdf'))
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color: #ef4444;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="tqv-attachment-info">
                                <div class="tqv-attachment-name">{{ $attachment->file_name }}</div>
                                <div class="tqv-attachment-size">{{ formatFileSizeForQuickView($attachment->file_size) }}</div>
                            </div>
                            <div class="tqv-attachment-actions">
                                @php
                                    $isPreviewable = in_array($attachment->file_type, [
                                        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'
                                    ]);
                                @endphp
                                @if($isPreviewable)
                                    <a href="{{ route('attachments.task.preview', $attachment) }}" target="_blank" class="tqv-attachment-btn preview" title="Preview">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                @endif
                                <a href="{{ route('attachments.task.download', $attachment) }}" class="tqv-attachment-btn download" title="Download">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Recent Comments --}}
        @if($task->comments->count() > 0)
            <div class="tqv-divider"></div>
            <div class="tqv-section">
                <div class="tqv-label">Recent Comments ({{ $task->comments->count() }})</div>
                <div class="tqv-comments">
                    @foreach($task->comments->take(3) as $comment)
                        <div class="tqv-comment">
                            <div class="tqv-comment-header">
                                <span class="tqv-comment-author">{{ $comment->author->user->name ?? 'Unknown' }}</span>
                                <span class="tqv-comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="tqv-comment-body">{{ Str::limit(strip_tags($comment->comment), 150) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 40px; color: #9ca3af;">
            Task not found
        </div>
    @endif
</div>
