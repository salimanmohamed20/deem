<x-filament-panels::page>
    <style>
        .pt-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .pt-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            padding: 16px 20px;
            background: white;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dark .pt-controls {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .pt-controls-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pt-controls-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pt-select {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: white;
            font-size: 14px;
            color: #374151;
            min-width: 200px;
        }

        .dark .pt-select {
            background: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }

        .pt-nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: white;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.15s;
        }

        .dark .pt-nav-btn {
            background: #374151;
            border-color: #4b5563;
            color: #9ca3af;
        }

        .pt-nav-btn:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .dark .pt-nav-btn:hover {
            background: #4b5563;
            color: #f3f4f6;
        }

        .pt-nav-btn svg {
            width: 16px;
            height: 16px;
        }

        .pt-period-label {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            min-width: 180px;
            text-align: center;
        }

        .dark .pt-period-label {
            color: #f3f4f6;
        }

        .pt-today-btn {
            padding: 8px 16px;
            border-radius: 8px;
            border: none;
            background: #3b82f6;
            color: white;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
        }

        .pt-today-btn:hover {
            background: #2563eb;
        }

        .pt-view-toggle {
            display: flex;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .dark .pt-view-toggle {
            border-color: #4b5563;
        }

        .pt-view-btn {
            padding: 8px 14px;
            border: none;
            background: white;
            color: #6b7280;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
        }

        .dark .pt-view-btn {
            background: #374151;
            color: #9ca3af;
        }

        .pt-view-btn.active {
            background: #3b82f6;
            color: white;
        }

        .pt-view-btn:not(:last-child) {
            border-right: 1px solid #e5e7eb;
        }

        .dark .pt-view-btn:not(:last-child) {
            border-right-color: #4b5563;
        }

        .pt-timeline {
            background: white;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .dark .pt-timeline {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .pt-timeline-header {
            display: flex;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .dark .pt-timeline-header {
            background: #111827;
            border-bottom-color: #374151;
        }

        .pt-task-column {
            width: 280px;
            min-width: 280px;
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-right: 1px solid #e5e7eb;
        }

        .dark .pt-task-column {
            border-right-color: #374151;
            color: #9ca3af;
        }

        .pt-dates-header {
            flex: 1;
            display: flex;
            overflow-x: auto;
        }

        .pt-date-cell {
            flex: 1;
            min-width: 40px;
            padding: 12px 4px;
            text-align: center;
            font-size: 11px;
            font-weight: 500;
            color: #6b7280;
            border-right: 1px solid #f3f4f6;
        }

        .dark .pt-date-cell {
            color: #9ca3af;
            border-right-color: #374151;
        }

        .pt-date-cell.today {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            font-weight: 600;
        }

        .pt-date-cell.weekend {
            background: #f9fafb;
        }

        .dark .pt-date-cell.weekend {
            background: rgba(255, 255, 255, 0.02);
        }

        .pt-timeline-body {
            max-height: 500px;
            overflow-y: auto;
        }

        .pt-task-row {
            display: flex;
            border-bottom: 1px solid #f3f4f6;
            min-height: 60px;
        }

        .dark .pt-task-row {
            border-bottom-color: #374151;
        }

        .pt-task-row:hover {
            background: #f9fafb;
        }

        .dark .pt-task-row:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .pt-task-info {
            width: 280px;
            min-width: 280px;
            padding: 12px 16px;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .dark .pt-task-info {
            border-right-color: #374151;
        }

        .pt-task-title {
            font-size: 14px;
            font-weight: 500;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }

        .dark .pt-task-title {
            color: #f3f4f6;
        }

        .pt-task-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pt-task-status {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 10px;
            text-transform: uppercase;
        }

        .pt-task-status.to_do {
            background: rgba(251, 191, 36, 0.15);
            color: #b45309;
        }

        .pt-task-status.in_progress {
            background: rgba(59, 130, 246, 0.15);
            color: #1d4ed8;
        }

        .pt-task-status.done {
            background: rgba(34, 197, 94, 0.15);
            color: #15803d;
        }

        .dark .pt-task-status.to_do {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }

        .dark .pt-task-status.in_progress {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .dark .pt-task-status.done {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .pt-task-assignees {
            display: flex;
            margin-left: auto;
        }

        .pt-assignee-avatar {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: 600;
            color: white;
            margin-left: -6px;
            border: 2px solid white;
        }

        .dark .pt-assignee-avatar {
            border-color: #1f2937;
        }

        .pt-assignee-avatar:first-child {
            margin-left: 0;
        }

        .pt-gantt-area {
            flex: 1;
            position: relative;
            min-height: 36px;
        }

        .pt-gantt-grid {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
        }

        .pt-gantt-cell {
            flex: 1;
            border-right: 1px solid #f3f4f6;
        }

        .dark .pt-gantt-cell {
            border-right-color: rgba(255, 255, 255, 0.05);
        }

        .pt-gantt-cell.today {
            background: rgba(59, 130, 246, 0.05);
        }

        .pt-gantt-cell.weekend {
            background: rgba(0, 0, 0, 0.02);
        }

        .dark .pt-gantt-cell.weekend {
            background: rgba(255, 255, 255, 0.01);
        }

        .pt-gantt-bar {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            font-size: 11px;
            font-weight: 500;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            transition: all 0.15s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .pt-gantt-bar:hover {
            transform: translateY(-50%) scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .pt-gantt-bar.to_do {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        .pt-gantt-bar.in_progress {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
        }

        .pt-gantt-bar.done {
            background: linear-gradient(135deg, #4ade80, #22c55e);
        }

        .pt-gantt-bar.overdue {
            background: linear-gradient(135deg, #f87171, #ef4444);
        }

        .pt-empty {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .pt-empty svg {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            opacity: 0.5;
        }

        .pt-project-info {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            background: white;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dark .pt-project-info {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .pt-project-stat {
            text-align: center;
            padding: 0 20px;
            border-right: 1px solid #e5e7eb;
        }

        .dark .pt-project-stat {
            border-right-color: #374151;
        }

        .pt-project-stat:last-child {
            border-right: none;
        }

        .pt-project-stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
        }

        .dark .pt-project-stat-value {
            color: #f3f4f6;
        }

        .pt-project-stat-label {
            font-size: 12px;
            color: #6b7280;
        }

        .dark .pt-project-stat-label {
            color: #9ca3af;
        }
    </style>

    <div class="pt-container">
        {{-- Controls --}}
        <div class="pt-controls">
            <div class="pt-controls-left">
                <select wire:model.live="selectedProject" class="pt-select">
                    <option value="">Select Project</option>
                    @foreach($this->getProjectOptions() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>

                <div class="pt-view-toggle">
                    <button wire:click="$set('viewMode', 'week')" 
                            class="pt-view-btn {{ $viewMode === 'week' ? 'active' : '' }}">
                        Week
                    </button>
                    <button wire:click="$set('viewMode', 'month')" 
                            class="pt-view-btn {{ $viewMode === 'month' ? 'active' : '' }}">
                        Month
                    </button>
                    <button wire:click="$set('viewMode', 'quarter')" 
                            class="pt-view-btn {{ $viewMode === 'quarter' ? 'active' : '' }}">
                        Quarter
                    </button>
                </div>
            </div>

            <div class="pt-controls-right">
                <button wire:click="previousPeriod" class="pt-nav-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/>
                    </svg>
                </button>
                
                <span class="pt-period-label">{{ $this->getPeriodLabel() }}</span>
                
                <button wire:click="nextPeriod" class="pt-nav-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <button wire:click="goToToday" class="pt-today-btn">Today</button>
            </div>
        </div>

        @if($selectedProject && $project)
            {{-- Project Stats --}}
            <div class="pt-project-info">
                @php
                    $totalTasks = count($tasks);
                    $doneTasks = collect($tasks)->where('status', 'done')->count();
                    $overdueTasks = collect($tasks)->where('is_overdue', true)->count();
                    $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
                @endphp
                <div class="pt-project-stat">
                    <div class="pt-project-stat-value">{{ $totalTasks }}</div>
                    <div class="pt-project-stat-label">Total Tasks</div>
                </div>
                <div class="pt-project-stat">
                    <div class="pt-project-stat-value" style="color: #22c55e;">{{ $doneTasks }}</div>
                    <div class="pt-project-stat-label">Completed</div>
                </div>
                <div class="pt-project-stat">
                    <div class="pt-project-stat-value" style="color: {{ $overdueTasks > 0 ? '#ef4444' : '#22c55e' }};">{{ $overdueTasks }}</div>
                    <div class="pt-project-stat-label">Overdue</div>
                </div>
                <div class="pt-project-stat">
                    <div class="pt-project-stat-value">{{ $progress }}%</div>
                    <div class="pt-project-stat-label">Progress</div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="pt-timeline">
                {{-- Header --}}
                <div class="pt-timeline-header">
                    <div class="pt-task-column">Task</div>
                    <div class="pt-dates-header">
                        @foreach($timelineData as $date)
                            <div class="pt-date-cell {{ $date['is_today'] ? 'today' : '' }} {{ $date['is_weekend'] ? 'weekend' : '' }}">
                                {{ $date['label'] }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Body --}}
                <div class="pt-timeline-body">
                    @forelse($tasks as $task)
                        <div class="pt-task-row">
                            <div class="pt-task-info">
                                <div class="pt-task-title">{{ $task['title'] }}</div>
                                <div class="pt-task-meta">
                                    <span class="pt-task-status {{ $task['status'] }}">
                                        {{ str_replace('_', ' ', $task['status']) }}
                                    </span>
                                    @if(count($task['assignees']) > 0)
                                        <div class="pt-task-assignees">
                                            @foreach(array_slice($task['assignees'], 0, 3) as $assignee)
                                                <div class="pt-assignee-avatar" title="{{ $assignee['name'] }}">
                                                    {{ $assignee['initials'] }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="pt-gantt-area">
                                <div class="pt-gantt-grid">
                                    @foreach($timelineData as $date)
                                        <div class="pt-gantt-cell {{ $date['is_today'] ? 'today' : '' }} {{ $date['is_weekend'] ? 'weekend' : '' }}"></div>
                                    @endforeach
                                </div>
                                @if($task['visible'])
                                    <div class="pt-gantt-bar {{ $task['is_overdue'] ? 'overdue' : $task['status'] }}"
                                         style="left: {{ $task['left'] }}%; width: {{ $task['width'] }}%;"
                                         title="{{ $task['title'] }}{{ $task['deadline'] ? ' - Due: ' . \Carbon\Carbon::parse($task['deadline'])->format('M j, Y') : '' }}">
                                        @if($task['width'] > 10)
                                            {{ Str::limit($task['title'], 20) }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="pt-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                            </svg>
                            <div style="font-size: 16px; font-weight: 500;">No tasks in this project</div>
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            <div class="pt-timeline">
                <div class="pt-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
                    </svg>
                    <div style="font-size: 16px; font-weight: 500;">Select a project to view timeline</div>
                    <div style="font-size: 14px; margin-top: 4px; color: #9ca3af;">Choose a project from the dropdown above</div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
