<x-filament-panels::page>
    <style>
        .task-board-container { padding: 0; }
        .task-board-filters {
            display: flex; align-items: flex-end; gap: 16px; margin-bottom: 24px;
            padding: 16px 20px; background: white; border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05);
        }
        .dark .task-board-filters { background: #1f2937; border-color: rgba(255,255,255,0.05); }
        .task-board-filters .fi-fo-component-ctn, .task-board-filters > div, .task-board-filters > form > div {
            display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important;
            gap: 16px !important; align-items: flex-end !important; width: 100% !important;
        }
        .task-board-filters .fi-fo-component-ctn > div, .task-board-filters > div > div, .task-board-filters > form > div > div {
            flex: 1 1 180px !important; min-width: 140px !important; max-width: 220px !important;
        }
        .task-board-filters [data-field-type="toggle"], .task-board-filters .fi-fo-toggle {
            flex: 0 0 auto !important; min-width: auto !important; max-width: none !important; padding-bottom: 10px;
        }
        .task-board-filters .fi-fo-field-wrp { margin-bottom: 0; }
        @media (max-width: 900px) {
            .task-board-filters .fi-fo-component-ctn, .task-board-filters > div, .task-board-filters > form > div { flex-wrap: wrap !important; }
        }

        /* Swimlane styles */
        .swimlane { margin-bottom: 24px; }
        .swimlane-header {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            background: #f3f4f6; border-radius: 10px; margin-bottom: 16px;
        }
        .dark .swimlane-header { background: #374151; }
        .swimlane-title { font-weight: 600; font-size: 15px; color: #1f2937; }
        .dark .swimlane-title { color: #f9fafb; }
        .swimlane-count { font-size: 13px; color: #6b7280; }

        .task-columns-wrapper {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding-bottom: 16px;
        }
        @media (max-width: 1024px) { .task-columns-wrapper { grid-template-columns: 1fr; gap: 16px; } }

        .task-column {
            min-width: 0; border-radius: 16px; display: flex; flex-direction: column;
            padding: 16px; min-height: 300px;
        }
        .task-column-todo { background: linear-gradient(180deg, #fef9c3 0%, #fefce8 100%); border: 1px solid rgba(250,204,21,0.2); }
        .task-column-progress { background: linear-gradient(180deg, #dbeafe 0%, #eff6ff 100%); border: 1px solid rgba(59,130,246,0.2); }
        .task-column-done { background: linear-gradient(180deg, #dcfce7 0%, #f0fdf4 100%); border: 1px solid rgba(34,197,94,0.2); }
        .dark .task-column-todo { background: linear-gradient(180deg, rgba(250,204,21,0.12) 0%, rgba(250,204,21,0.04) 100%); border-color: rgba(250,204,21,0.15); }
        .dark .task-column-progress { background: linear-gradient(180deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.04) 100%); border-color: rgba(59,130,246,0.15); }
        .dark .task-column-done { background: linear-gradient(180deg, rgba(34,197,94,0.12) 0%, rgba(34,197,94,0.04) 100%); border-color: rgba(34,197,94,0.15); }

        .task-column-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding: 0 4px; }
        .task-column-header-left { display: flex; align-items: center; gap: 10px; }
        .task-column-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .task-column-icon svg { width: 16px; height: 16px; }
        .task-column-todo .task-column-icon { background: rgba(250,204,21,0.2); color: #ca8a04; }
        .task-column-progress .task-column-icon { background: rgba(59,130,246,0.2); color: #2563eb; }
        .task-column-done .task-column-icon { background: rgba(34,197,94,0.2); color: #16a34a; }
        .dark .task-column-todo .task-column-icon { background: rgba(250,204,21,0.15); color: #facc15; }
        .dark .task-column-progress .task-column-icon { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .dark .task-column-done .task-column-icon { background: rgba(34,197,94,0.15); color: #4ade80; }
        .task-column-title { font-weight: 600; font-size: 14px; color: #374151; letter-spacing: -0.01em; }
        .dark .task-column-title { color: #f3f4f6; }
        .task-count-badge { min-width: 24px; height: 24px; padding: 0 8px; display: flex; align-items: center; justify-content: center; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .task-column-todo .task-count-badge { background: rgba(250,204,21,0.25); color: #a16207; }
        .task-column-progress .task-count-badge { background: rgba(59,130,246,0.2); color: #1d4ed8; }
        .task-column-done .task-count-badge { background: rgba(34,197,94,0.2); color: #15803d; }
        .dark .task-column-todo .task-count-badge { background: rgba(250,204,21,0.2); color: #fde047; }
        .dark .task-column-progress .task-count-badge { background: rgba(59,130,246,0.2); color: #93c5fd; }
        .dark .task-column-done .task-count-badge { background: rgba(34,197,94,0.2); color: #86efac; }

        .task-cards { display: flex; flex-direction: column; gap: 12px; flex: 1; }
        .task-card {
            background: white; border-radius: 12px; padding: 16px; cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.2s ease; border: 1px solid rgba(0,0,0,0.04);
            position: relative; overflow: hidden;
        }
        .dark .task-card { background: #1f2937; border-color: rgba(255,255,255,0.06); box-shadow: 0 1px 3px rgba(0,0,0,0.3); }
        .task-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .dark .task-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.4); border-color: rgba(255,255,255,0.1); }
        .task-card.dragging { opacity: 0.5; transform: rotate(2deg); }
        .task-card.drag-over { border-top: 3px solid #3b82f6; }
        .task-card-priority-indicator { position: absolute; left: 0; top: 0; bottom: 0; width: 4px; }
        .task-card-priority-indicator.high { background: linear-gradient(180deg, #ef4444, #dc2626); }
        .task-card-priority-indicator.medium { background: linear-gradient(180deg, #f59e0b, #d97706); }
        .task-card-priority-indicator.low { background: linear-gradient(180deg, #22c55e, #16a34a); }
        .task-card-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
        .task-card-title { font-weight: 600; font-size: 14px; color: #1f2937; line-height: 1.4; flex: 1; }
        .dark .task-card-title { color: #f9fafb; }
        .task-priority-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; flex-shrink: 0; }
        .task-priority-badge.high { background: rgba(239,68,68,0.1); color: #dc2626; }
        .task-priority-badge.medium { background: rgba(245,158,11,0.1); color: #d97706; }
        .task-priority-badge.low { background: rgba(34,197,94,0.1); color: #16a34a; }
        .dark .task-priority-badge.high { background: rgba(239,68,68,0.15); color: #f87171; }
        .dark .task-priority-badge.medium { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .dark .task-priority-badge.low { background: rgba(34,197,94,0.15); color: #4ade80; }
        .task-card-project { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; background: #f3f4f6; border-radius: 8px; font-size: 12px; color: #4b5563; font-weight: 500; margin-bottom: 14px; }
        .dark .task-card-project { background: rgba(255,255,255,0.06); color: #9ca3af; }
        .task-card-project svg { width: 12px; height: 12px; opacity: 0.7; }

        /* Subtask progress */
        .task-subtask-progress { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
        .task-subtask-bar { flex: 1; height: 4px; background: #e5e7eb; border-radius: 2px; overflow: hidden; }
        .dark .task-subtask-bar { background: #374151; }
        .task-subtask-bar-fill { height: 100%; background: #22c55e; border-radius: 2px; transition: width 0.3s; }
        .task-subtask-text { font-size: 11px; color: #6b7280; font-weight: 500; }

        .task-card-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 12px; border-top: 1px solid rgba(0,0,0,0.05); margin-top: auto; }
        .dark .task-card-footer { border-top-color: rgba(255,255,255,0.06); }
        .task-assignee { display: flex; align-items: center; gap: 8px; }
        .task-assignee-avatar { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #6366f1); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; color: white; flex-shrink: 0; box-shadow: 0 2px 4px rgba(99,102,241,0.3); }
        .task-assignee-name { font-size: 12px; color: #4b5563; font-weight: 500; }
        .dark .task-assignee-name { color: #d1d5db; }
        .task-date { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #9ca3af; font-weight: 500; }
        .task-date svg { width: 12px; height: 12px; }
        .task-date.overdue { color: #ef4444; }
        .task-date.soon { color: #f59e0b; }
        .task-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; color: #9ca3af; font-size: 13px; text-align: center; flex: 1; }
        .task-empty svg { width: 40px; height: 40px; margin-bottom: 12px; opacity: 0.4; }
        .task-empty-text { font-weight: 500; }
        .column-drag-over { outline: 2px dashed #3b82f6; outline-offset: -2px; background: rgba(59,130,246,0.05) !important; }
        .dark .column-drag-over { background: rgba(59,130,246,0.1) !important; }
    </style>

    <div x-data="taskBoard()" class="task-board-container">
        <div class="task-board-filters">{{ $this->form }}</div>

        @php
            $columns = [
                'to_do' => ['label' => 'To Do', 'class' => 'task-column-todo', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><circle cx="10" cy="10" r="6" fill="none" stroke="currentColor" stroke-width="2"/></svg>'],
                'in_progress' => ['label' => 'In Progress', 'class' => 'task-column-progress', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/></svg>'],
                'done' => ['label' => 'Done', 'class' => 'task-column-done', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>'],
            ];
        @endphp

        @if(count($this->swimlanes) > 0)
            {{-- Swimlane View --}}
            @foreach($this->swimlanes as $swimlane)
                <div class="swimlane">
                    <div class="swimlane-header">
                        <span class="swimlane-title">{{ $swimlane['name'] }}</span>
                        <span class="swimlane-count">
                            {{ count($swimlane['tasks']['to_do'] ?? []) + count($swimlane['tasks']['in_progress'] ?? []) + count($swimlane['tasks']['done'] ?? []) }} tasks
                        </span>
                    </div>
                    <div class="task-columns-wrapper">
                        @foreach($columns as $status => $column)
                            @include('filament.pages.partials.task-column', [
                                'status' => $status,
                                'column' => $column,
                                'tasks' => $swimlane['tasks'][$status] ?? collect([]),
                                'swimlaneId' => $swimlane['id'],
                            ])
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            {{-- Standard View --}}
            <div class="task-columns-wrapper">
                @foreach($columns as $status => $column)
                    @include('filament.pages.partials.task-column', [
                        'status' => $status,
                        'column' => $column,
                        'tasks' => $this->tasks[$status] ?? collect([]),
                        'swimlaneId' => null,
                    ])
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function taskBoard() {
            return {
                draggedTask: null, draggedFromStatus: null, dragOverColumn: null,
                dragOverCard: null, dropIndex: null, isDragging: false,
                startDrag(event, taskId, fromStatus) {
                    this.isDragging = true; this.draggedTask = taskId; this.draggedFromStatus = fromStatus;
                    event.dataTransfer.effectAllowed = 'move'; event.dataTransfer.setData('text/plain', taskId);
                },
                endDrag() {
                    setTimeout(() => { this.isDragging = false; }, 100);
                    this.draggedTask = null; this.draggedFromStatus = null;
                    this.dragOverColumn = null; this.dragOverCard = null; this.dropIndex = null;
                },
                handleColumnDragOver(event, status) { this.dragOverColumn = status; },
                handleColumnDragLeave(event) {
                    if (!event.relatedTarget || !event.currentTarget.contains(event.relatedTarget)) this.dragOverColumn = null;
                },
                handleCardDragOver(event, cardId, index) {
                    if (this.draggedTask === cardId) return;
                    const rect = event.currentTarget.getBoundingClientRect();
                    this.dropIndex = event.clientY < rect.top + rect.height / 2 ? index : index + 1;
                    this.dragOverCard = cardId;
                },
                handleCardDragLeave(event) {
                    if (!event.relatedTarget || !event.currentTarget.contains(event.relatedTarget)) this.dragOverCard = null;
                },
                handleDrop(event, status) {
                    if (!this.draggedTask) return;
                    let newIndex = this.dropIndex ?? event.currentTarget.querySelector('.task-cards').querySelectorAll('.task-card').length;
                    this.$wire.updateTaskStatus(this.draggedTask, status, newIndex);
                    this.endDrag();
                }
            }
        }
    </script>
</x-filament-panels::page>
