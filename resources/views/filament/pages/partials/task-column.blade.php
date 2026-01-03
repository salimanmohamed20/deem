<div class="task-column {{ $column['class'] }}"
     data-status="{{ $status }}"
     :class="{ 'column-drag-over': dragOverColumn === '{{ $status }}' && !dragOverCard }"
     @dragover.prevent="handleColumnDragOver($event, '{{ $status }}')"
     @dragleave="handleColumnDragLeave($event)"
     @drop="handleDrop($event, '{{ $status }}')">
    
    <div class="task-column-header">
        <div class="task-column-header-left">
            <div class="task-column-icon">{!! $column['icon'] !!}</div>
            <span class="task-column-title">{{ $column['label'] }}</span>
        </div>
        <span class="task-count-badge">{{ is_countable($tasks) ? count($tasks) : $tasks->count() }}</span>
    </div>

    <div class="task-cards" data-status="{{ $status }}">
        @forelse($tasks as $index => $task)
            <div class="task-card"
                 data-task-id="{{ $task->id }}"
                 data-index="{{ $index }}"
                 draggable="true"
                 :class="{ 'dragging': draggedTask === {{ $task->id }}, 'drag-over': dragOverCard === {{ $task->id }} }"
                 @click="if (!isDragging) $wire.openTaskQuickView({{ $task->id }})"
                 @dragstart="startDrag($event, {{ $task->id }}, '{{ $status }}')"
                 @dragend="endDrag()"
                 @dragover.prevent="handleCardDragOver($event, {{ $task->id }}, {{ $index }})"
                 @dragleave="handleCardDragLeave($event)">
                
                <div class="task-card-priority-indicator {{ $task->priority }}"></div>

                <div class="task-card-header">
                    <div class="task-card-title">{{ $task->title }}</div>
                    <span class="task-priority-badge {{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                </div>
                
                <div class="task-card-project">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3.75 3A1.75 1.75 0 0 0 2 4.75v10.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0 0 18 15.25v-8.5A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75Z" clip-rule="evenodd" />
                    </svg>
                    {{ $task->project->name }}
                </div>

                @if($task->subtasks && $task->subtasks->count() > 0)
                    @php
                        $totalSubtasks = $task->subtasks->count();
                        $completedSubtasks = $task->subtasks->where('is_completed', true)->count();
                        $progress = $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100) : 0;
                    @endphp
                    <div class="task-subtask-progress">
                        <div class="task-subtask-bar">
                            <div class="task-subtask-bar-fill" style="width: {{ $progress }}%"></div>
                        </div>
                        <span class="task-subtask-text">{{ $completedSubtasks }}/{{ $totalSubtasks }}</span>
                    </div>
                @endif
                
                <div class="task-card-footer">
                    @if($task->assignees->count() > 0)
                        <div class="task-assignee">
                            <span class="task-assignee-avatar">{{ strtoupper(substr($task->assignees->first()->user->name ?? 'U', 0, 2)) }}</span>
                            <span class="task-assignee-name">{{ $task->assignees->first()->user->name ?? 'Unassigned' }}</span>
                        </div>
                    @else
                        <div class="task-assignee">
                            <span class="task-assignee-name" style="color: #9ca3af;">Unassigned</span>
                        </div>
                    @endif
                    
                    @if($task->deadline)
                        @php
                            $deadline = \Carbon\Carbon::parse($task->deadline);
                            $isOverdue = $deadline->isPast() && $status !== 'done';
                            $isSoon = !$isOverdue && $deadline->diffInDays(now()) <= 2 && $status !== 'done';
                        @endphp
                        <div class="task-date {{ $isOverdue ? 'overdue' : ($isSoon ? 'soon' : '') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd"/>
                            </svg>
                            {{ $deadline->format('M j') }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="task-empty">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
                <span class="task-empty-text">No tasks yet</span>
            </div>
        @endforelse
    </div>
</div>
