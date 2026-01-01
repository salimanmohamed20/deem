<x-filament-panels::page>
    <style>
        .task-board-filters {
            display: flex;
            align-items: flex-end;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        
        .task-columns-wrapper {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 16px;
            align-items: flex-start;
        }
        
        .task-column {
            min-width: 260px;
            width: 260px;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            padding: 12px;
        }
        
        .task-column-todo {
            background: linear-gradient(180deg, #fef3c7 0%, #fefce8 100%);
        }
        
        .task-column-progress {
            background: linear-gradient(180deg, #fed7aa 0%, #fff7ed 100%);
        }
        
        .task-column-done {
            background: linear-gradient(180deg, #bbf7d0 0%, #f0fdf4 100%);
        }
        
        .dark .task-column-todo {
            background: linear-gradient(180deg, rgba(253, 224, 71, 0.15) 0%, rgba(253, 224, 71, 0.05) 100%);
        }
        
        .dark .task-column-progress {
            background: linear-gradient(180deg, rgba(251, 146, 60, 0.15) 0%, rgba(251, 146, 60, 0.05) 100%);
        }
        
        .dark .task-column-done {
            background: linear-gradient(180deg, rgba(74, 222, 128, 0.15) 0%, rgba(74, 222, 128, 0.05) 100%);
        }
        
        .task-column-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding: 0 4px;
        }
        
        .task-column-title {
            font-weight: 600;
            font-size: 13px;
            color: #374151;
        }
        
        .dark .task-column-title {
            color: #e5e7eb;
        }
        
        .task-count-badge {
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.08);
            border-radius: 50%;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
        }
        
        .dark .task-count-badge {
            background: rgba(255, 255, 255, 0.15);
            color: #9ca3af;
        }
        
        .task-cards {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 100px;
        }
        
        .task-card {
            background: white;
            border-radius: 12px;
            padding: 14px;
            cursor: grab;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.15s ease;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }
        
        .dark .task-card {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        .task-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }
        
        .task-card.dragging {
            opacity: 0.4;
        }
        
        .task-card-title {
            font-weight: 600;
            font-size: 13px;
            color: #1f2937;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .dark .task-card-title {
            color: #f3f4f6;
        }
        
        .task-card-project {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            background: #f3f4f6;
            border-radius: 6px;
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        
        .dark .task-card-project {
            background: rgba(255, 255, 255, 0.08);
            color: #9ca3af;
        }
        
        .task-card-project svg {
            width: 11px;
            height: 11px;
        }
        
        .task-priority {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 14px;
        }
        
        .task-priority-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }
        
        .task-priority-high .task-priority-dot { background: #ef4444; }
        .task-priority-high { color: #ef4444; }
        
        .task-priority-medium .task-priority-dot { background: #f59e0b; }
        .task-priority-medium { color: #f59e0b; }
        
        .task-priority-low .task-priority-dot { background: #22c55e; }
        .task-priority-low { color: #22c55e; }
        
        .task-card-footer {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .task-assignee-avatar {
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
            flex-shrink: 0;
        }
        
        .task-assignee-name {
            font-size: 12px;
            color: #374151;
            font-weight: 500;
        }
        
        .dark .task-assignee-name {
            color: #d1d5db;
        }
        
        .task-date {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 8px;
        }
        
        .task-empty {
            text-align: center;
            padding: 24px 12px;
            color: #9ca3af;
            font-size: 13px;
        }
        
        .column-drag-over {
            outline: 2px dashed #3b82f6;
            outline-offset: -2px;
        }
    </style>

    <div x-data="taskBoard()">
        {{-- Filters --}}
        <div class="task-board-filters">
            {{ $this->form }}
        </div>

        {{-- Columns --}}
        <div class="task-columns-wrapper">
            @php
                $columns = [
                    'to_do' => ['label' => 'To Do', 'class' => 'task-column-todo'],
                    'in_progress' => ['label' => 'In Progress', 'class' => 'task-column-progress'],
                    'done' => ['label' => 'Done', 'class' => 'task-column-done'],
                ];
            @endphp

            @foreach($columns as $status => $column)
                <div class="task-column {{ $column['class'] }}"
                     :class="{ 'column-drag-over': dragOverColumn === '{{ $status }}' }"
                     @dragover.prevent="dragOverColumn = '{{ $status }}'"
                     @dragleave="dragOverColumn = null"
                     @drop="handleDrop('{{ $status }}')">
                    
                    {{-- Header --}}
                    <div class="task-column-header">
                        <span class="task-column-title">{{ $column['label'] }}</span>
                        <span class="task-count-badge">{{ count($this->tasks[$status] ?? []) }}</span>
                    </div>

                    {{-- Cards --}}
                    <div class="task-cards">
                        @forelse($this->tasks[$status] ?? [] as $task)
                            <div class="task-card"
                                 draggable="true"
                                 :class="{ 'dragging': draggedTask === {{ $task->id }} }"
                                 @dragstart="startDrag({{ $task->id }})"
                                 @dragend="endDrag()">
                                
                                {{-- Title --}}
                                <div class="task-card-title">{{ $task->title }}</div>
                                
                                {{-- Project --}}
                                <div class="task-card-project">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3.75 3A1.75 1.75 0 0 0 2 4.75v10.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0 0 18 15.25v-8.5A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75Z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $task->project->name }}
                                </div>
                                
                                {{-- Priority --}}
                                <div class="task-priority task-priority-{{ $task->priority }}">
                                    <span class="task-priority-dot"></span>
                                    {{ ucfirst($task->priority) }}
                                </div>
                                
                                {{-- Assignee --}}
                                @if($task->assignees->count() > 0)
                                    <div class="task-card-footer">
                                        <span class="task-assignee-avatar">
                                            {{ strtoupper(substr($task->assignees->first()->user->name ?? 'U', 0, 2)) }}
                                        </span>
                                        <span class="task-assignee-name">
                                            {{ $task->assignees->first()->user->name ?? 'Unassigned' }}
                                        </span>
                                    </div>
                                @endif
                                
                                {{-- Date --}}
                                @if($task->deadline)
                                    <div class="task-date">
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('M j') }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="task-empty">
                                No tasks in this column
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function taskBoard() {
            return {
                draggedTask: null,
                dragOverColumn: null,
                
                startDrag(taskId) {
                    this.draggedTask = taskId;
                },
                
                endDrag() {
                    this.draggedTask = null;
                    this.dragOverColumn = null;
                },
                
                handleDrop(status) {
                    if (this.draggedTask) {
                        this.$wire.updateTaskStatus(this.draggedTask, status);
                    }
                    this.dragOverColumn = null;
                }
            }
        }
    </script>
</x-filament-panels::page>
