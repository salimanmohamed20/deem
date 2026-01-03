<x-filament-panels::page>
    <style>
        .fm-container { max-width: 900px; margin: 0 auto; }
        .fm-header { text-align: center; margin-bottom: 24px; }
        .fm-progress-wrap { margin-bottom: 16px; }
        .fm-progress-bar { height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; }
        .dark .fm-progress-bar { background: #374151; }
        .fm-progress-fill { height: 100%; background: linear-gradient(90deg, #22c55e, #16a34a); border-radius: 3px; transition: width 0.3s; }
        .fm-progress-text { font-size: 13px; color: #6b7280; margin-top: 8px; }
        .dark .fm-progress-text { color: #9ca3af; }
        .fm-queue-info { font-size: 14px; color: #6b7280; }
        .dark .fm-queue-info { color: #9ca3af; }

        .fm-card { background: white; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); overflow: hidden; }
        .dark .fm-card { background: #1f2937; border-color: rgba(255,255,255,0.05); }
        .fm-task-header { padding: 24px; border-bottom: 1px solid #f3f4f6; }
        .dark .fm-task-header { border-bottom-color: #374151; }
        .fm-task-badges { display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
        .fm-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .fm-badge.project { background: #f3f4f6; color: #374151; }
        .dark .fm-badge.project { background: #374151; color: #d1d5db; }
        .fm-badge.priority-high { background: rgba(239,68,68,0.1); color: #dc2626; }
        .fm-badge.priority-medium { background: rgba(245,158,11,0.1); color: #d97706; }
        .fm-badge.priority-low { background: rgba(34,197,94,0.1); color: #16a34a; }
        .fm-badge.status-to_do { background: rgba(251,191,36,0.15); color: #b45309; }
        .fm-badge.status-in_progress { background: rgba(59,130,246,0.15); color: #1d4ed8; }
        .fm-task-title { font-size: 24px; font-weight: 700; color: #1f2937; line-height: 1.3; }
        .dark .fm-task-title { color: #f9fafb; }
        .fm-task-meta { display: flex; align-items: center; gap: 16px; margin-top: 12px; font-size: 13px; color: #6b7280; }
        .dark .fm-task-meta { color: #9ca3af; }
        .fm-task-meta svg { width: 16px; height: 16px; }
        .fm-meta-item { display: flex; align-items: center; gap: 6px; }
        .fm-meta-item.overdue { color: #ef4444; font-weight: 600; }

        .fm-task-body { padding: 24px; }
        .fm-description { font-size: 15px; line-height: 1.7; color: #4b5563; }
        .dark .fm-description { color: #d1d5db; }
        .fm-description:empty::before { content: 'No description provided'; color: #9ca3af; font-style: italic; }
        .fm-section-title { font-size: 14px; font-weight: 600; color: #1f2937; margin: 24px 0 12px; display: flex; align-items: center; gap: 8px; }
        .dark .fm-section-title { color: #f3f4f6; }
        .fm-section-title svg { width: 16px; height: 16px; color: #6b7280; }
        .fm-comments { max-height: 200px; overflow-y: auto; }
        .fm-comment { padding: 12px; background: #f9fafb; border-radius: 8px; margin-bottom: 8px; }
        .dark .fm-comment { background: rgba(255,255,255,0.04); }
        .fm-comment-header { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .fm-comment-author { font-size: 13px; font-weight: 600; color: #374151; }
        .dark .fm-comment-author { color: #f3f4f6; }
        .fm-comment-time { font-size: 11px; color: #9ca3af; }
        .fm-comment-text { font-size: 13px; color: #4b5563; }
        .dark .fm-comment-text { color: #d1d5db; }
        .fm-comment-form { display: flex; gap: 8px; margin-top: 12px; }
        .fm-comment-input { flex: 1; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: white; color: #1f2937; }
        .dark .fm-comment-input { background: #374151; border-color: #4b5563; color: #f3f4f6; }
        .fm-comment-btn { padding: 10px 16px; border: none; border-radius: 8px; background: #3b82f6; color: white; font-size: 13px; font-weight: 500; cursor: pointer; }
        .fm-comment-btn:hover { background: #2563eb; }

        .fm-actions { padding: 20px 24px; background: #f9fafb; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .dark .fm-actions { background: #111827; border-top-color: #374151; }
        .fm-nav-btns { display: flex; gap: 8px; }
        .fm-nav-btn { display: flex; align-items: center; gap: 6px; padding: 10px 16px; border: 1px solid #e5e7eb; border-radius: 8px; background: white; color: #374151; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.15s; }
        .dark .fm-nav-btn { background: #374151; border-color: #4b5563; color: #d1d5db; }
        .fm-nav-btn:hover:not(:disabled) { background: #f3f4f6; }
        .dark .fm-nav-btn:hover:not(:disabled) { background: #4b5563; }
        .fm-nav-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .fm-nav-btn svg { width: 16px; height: 16px; }
        .fm-action-btns { display: flex; gap: 8px; }
        .fm-btn { display: flex; align-items: center; gap: 6px; padding: 10px 20px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.15s; }
        .fm-btn svg { width: 18px; height: 18px; }
        .fm-btn.start { background: #3b82f6; color: white; }
        .fm-btn.start:hover { background: #2563eb; }
        .fm-btn.complete { background: #22c55e; color: white; }
        .fm-btn.complete:hover { background: #16a34a; }
        .fm-btn.skip { background: #f3f4f6; color: #6b7280; }
        .dark .fm-btn.skip { background: #374151; color: #9ca3af; }
        .fm-btn.skip:hover { background: #e5e7eb; }
        .dark .fm-btn.skip:hover { background: #4b5563; }

        .fm-confirm-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 50; }
        .fm-confirm-modal { background: white; border-radius: 16px; padding: 24px; max-width: 400px; text-align: center; }
        .dark .fm-confirm-modal { background: #1f2937; }
        .fm-confirm-icon { width: 60px; height: 60px; margin: 0 auto 16px; background: rgba(34,197,94,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .fm-confirm-icon svg { width: 30px; height: 30px; color: #22c55e; }
        .fm-confirm-title { font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 8px; }
        .dark .fm-confirm-title { color: #f9fafb; }
        .fm-confirm-text { font-size: 14px; color: #6b7280; margin-bottom: 20px; }
        .dark .fm-confirm-text { color: #9ca3af; }
        .fm-confirm-btns { display: flex; gap: 12px; justify-content: center; }
        .fm-confirm-btn { padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; }
        .fm-confirm-btn.cancel { background: #f3f4f6; border: none; color: #374151; }
        .dark .fm-confirm-btn.cancel { background: #374151; color: #d1d5db; }
        .fm-confirm-btn.confirm { background: #22c55e; border: none; color: white; }
        .fm-empty { text-align: center; padding: 80px 20px; }
        .fm-empty svg { width: 64px; height: 64px; margin: 0 auto 20px; color: #22c55e; }
        .fm-empty-title { font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 8px; }
        .dark .fm-empty-title { color: #f9fafb; }
        .fm-empty-text { font-size: 14px; color: #6b7280; }
        .dark .fm-empty-text { color: #9ca3af; }
        .fm-queue { display: flex; gap: 6px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
        .fm-queue-dot { width: 10px; height: 10px; border-radius: 50%; background: #e5e7eb; cursor: pointer; transition: all 0.15s; }
        .dark .fm-queue-dot { background: #374151; }
        .fm-queue-dot:hover { transform: scale(1.2); }
        .fm-queue-dot.active { background: #3b82f6; transform: scale(1.3); }
        .fm-queue-dot.high { background: #fecaca; }
        .fm-queue-dot.high.active { background: #ef4444; }
    </style>

    <div class="fm-container">
        @if($currentTask)
            {{-- Progress --}}
            <div class="fm-header">
                <div class="fm-progress-wrap">
                    <div class="fm-progress-bar">
                        <div class="fm-progress-fill" style="width: {{ $this->getProgress() }}%"></div>
                    </div>
                    <div class="fm-progress-text">Task {{ $currentIndex + 1 }} of {{ $taskQueue->count() }}</div>
                </div>
                
                {{-- Queue dots --}}
                <div class="fm-queue">
                    @foreach($taskQueue as $index => $task)
                        <div wire:click="selectTask({{ $index }})" 
                             class="fm-queue-dot {{ $index === $currentIndex ? 'active' : '' }} {{ $task->priority === 'high' ? 'high' : '' }}"
                             title="{{ $task->title }}">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Task Card --}}
            <div class="fm-card">
                <div class="fm-task-header">
                    <div class="fm-task-badges">
                        <span class="fm-badge project">{{ $currentTask->project->name }}</span>
                        <span class="fm-badge priority-{{ $currentTask->priority }}">{{ ucfirst($currentTask->priority) }} Priority</span>
                        <span class="fm-badge status-{{ $currentTask->status }}">{{ str_replace('_', ' ', ucfirst($currentTask->status)) }}</span>
                    </div>
                    <h1 class="fm-task-title">{{ $currentTask->title }}</h1>
                    <div class="fm-task-meta">
                        @if($currentTask->deadline)
                            @php
                                $deadline = \Carbon\Carbon::parse($currentTask->deadline);
                                $isOverdue = $deadline->isPast();
                            @endphp
                            <div class="fm-meta-item {{ $isOverdue ? 'overdue' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2z" clip-rule="evenodd"/>
                                </svg>
                                {{ $isOverdue ? 'Overdue: ' : 'Due: ' }}{{ $deadline->format('M j, Y') }}
                            </div>
                        @endif
                        @if($currentTask->assignees->count() > 0)
                            <div class="fm-meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z"/>
                                </svg>
                                {{ $currentTask->assignees->pluck('user.name')->join(', ') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="fm-task-body">
                    <div class="fm-description">{!! $currentTask->description !!}</div>

                    {{-- Comments --}}
                    <div class="fm-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2c-2.236 0-4.43.18-6.57.524C1.993 2.755 1 4.014 1 5.426v5.148c0 1.413.993 2.67 2.43 2.902 1.168.188 2.352.327 3.55.414.28.02.521.18.642.413l1.713 3.293a.75.75 0 001.33 0l1.713-3.293c.121-.233.362-.393.642-.413 1.198-.087 2.382-.226 3.55-.414 1.437-.232 2.43-1.49 2.43-2.902V5.426c0-1.413-.993-2.67-2.43-2.902A41.289 41.289 0 0010 2z" clip-rule="evenodd"/>
                        </svg>
                        Comments ({{ $currentTask->comments->count() }})
                    </div>
                    
                    <div class="fm-comments">
                        @forelse($currentTask->comments->sortByDesc('created_at')->take(5) as $comment)
                            <div class="fm-comment">
                                <div class="fm-comment-header">
                                    <span class="fm-comment-author">{{ $comment->author?->user?->name ?? 'Unknown' }}</span>
                                    <span class="fm-comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="fm-comment-text">{{ \Str::limit(strip_tags($comment->comment), 150) }}</div>
                            </div>
                        @empty
                            <div style="color: #9ca3af; font-size: 13px; padding: 12px;">No comments yet</div>
                        @endforelse
                    </div>

                    <div class="fm-comment-form">
                        <input type="text" wire:model="newComment" wire:keydown.enter="addComment" class="fm-comment-input" placeholder="Add a quick note...">
                        <button wire:click="addComment" class="fm-comment-btn">Add</button>
                    </div>
                </div>

                <div class="fm-actions">
                    <div class="fm-nav-btns">
                        <button wire:click="previousTask" class="fm-nav-btn" {{ $currentIndex === 0 ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/>
                            </svg>
                            Previous
                        </button>
                        <button wire:click="nextTask" class="fm-nav-btn" {{ $currentIndex >= $taskQueue->count() - 1 ? 'disabled' : '' }}>
                            Next
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <div class="fm-action-btns">
                        <button wire:click="skipTask" class="fm-btn skip">Skip</button>
                        @if($currentTask->status === 'to_do')
                            <button wire:click="startTask" class="fm-btn start">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                </svg>
                                Start Task
                            </button>
                        @else
                            <button wire:click="confirmComplete" class="fm-btn complete">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                </svg>
                                Complete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            {{-- All done! --}}
            <div class="fm-card">
                <div class="fm-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                    <div class="fm-empty-title">All caught up! 🎉</div>
                    <div class="fm-empty-text">You have no pending tasks. Great job!</div>
                </div>
            </div>
        @endif

        {{-- Complete Confirmation Modal --}}
        @if($showCompleteConfirm)
            <div class="fm-confirm-overlay" wire:click.self="cancelComplete">
                <div class="fm-confirm-modal">
                    <div class="fm-confirm-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="fm-confirm-title">Mark as Complete?</div>
                    <div class="fm-confirm-text">This will mark "{{ $currentTask?->title }}" as done.</div>
                    <div class="fm-confirm-btns">
                        <button wire:click="cancelComplete" class="fm-confirm-btn cancel">Cancel</button>
                        <button wire:click="completeTask" class="fm-confirm-btn confirm">Yes, Complete</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
