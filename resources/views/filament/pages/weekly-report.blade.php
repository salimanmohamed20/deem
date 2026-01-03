<x-filament-panels::page>
    <style>
        .wr-container { display: flex; flex-direction: column; gap: 24px; }
        .wr-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; padding: 20px 24px; background: white; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); }
        .dark .wr-header { background: #1f2937; border-color: rgba(255,255,255,0.05); }
        .wr-title { font-size: 20px; font-weight: 700; color: #1f2937; }
        .dark .wr-title { color: #f9fafb; }
        .wr-week-label { font-size: 14px; color: #6b7280; margin-top: 4px; }
        .dark .wr-week-label { color: #9ca3af; }
        .wr-nav { display: flex; align-items: center; gap: 12px; }
        .wr-nav-btn { display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; border: 1px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer; transition: all 0.15s; }
        .dark .wr-nav-btn { background: #374151; border-color: #4b5563; color: #9ca3af; }
        .wr-nav-btn:hover { background: #f3f4f6; color: #1f2937; }
        .dark .wr-nav-btn:hover { background: #4b5563; color: #f3f4f6; }
        .wr-nav-btn svg { width: 16px; height: 16px; }
        .wr-current-btn { padding: 8px 16px; border-radius: 8px; border: none; background: #3b82f6; color: white; font-size: 13px; font-weight: 500; cursor: pointer; }
        .wr-current-btn:hover { background: #2563eb; }
        .wr-current-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        .wr-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .wr-stat-card { background: white; border-radius: 12px; padding: 20px; border: 1px solid rgba(0,0,0,0.05); }
        .dark .wr-stat-card { background: #1f2937; border-color: rgba(255,255,255,0.05); }
        .wr-stat-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .wr-stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .wr-stat-icon svg { width: 20px; height: 20px; }
        .wr-stat-icon.success { background: rgba(34,197,94,0.1); color: #22c55e; }
        .wr-stat-icon.info { background: rgba(59,130,246,0.1); color: #3b82f6; }
        .wr-stat-icon.warning { background: rgba(245,158,11,0.1); color: #f59e0b; }
        .wr-stat-icon.danger { background: rgba(239,68,68,0.1); color: #ef4444; }
        .wr-stat-value { font-size: 32px; font-weight: 700; color: #1f2937; }
        .dark .wr-stat-value { color: #f9fafb; }
        .wr-stat-label { font-size: 13px; color: #6b7280; }
        .dark .wr-stat-label { color: #9ca3af; }
        .wr-stat-change { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 10px; margin-left: 8px; }
        .wr-stat-change.up { background: rgba(34,197,94,0.1); color: #22c55e; }
        .wr-stat-change.down { background: rgba(239,68,68,0.1); color: #ef4444; }
        .wr-stat-change.neutral { background: rgba(107,114,128,0.1); color: #6b7280; }

        .wr-sections { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        @media (max-width: 1024px) { .wr-sections { grid-template-columns: 1fr; } }
        .wr-section { background: white; border-radius: 12px; padding: 20px; border: 1px solid rgba(0,0,0,0.05); }
        .dark .wr-section { background: #1f2937; border-color: rgba(255,255,255,0.05); }
        .wr-section-title { font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .dark .wr-section-title { color: #f9fafb; }
        .wr-section-title svg { width: 18px; height: 18px; color: #6b7280; }
        .wr-project-item { display: flex; align-items: center; gap: 12px; padding: 12px; background: #f9fafb; border-radius: 8px; margin-bottom: 10px; }
        .dark .wr-project-item { background: rgba(255,255,255,0.04); }
        .wr-project-info { flex: 1; }
        .wr-project-name { font-size: 14px; font-weight: 500; color: #1f2937; }
        .dark .wr-project-name { color: #f3f4f6; }
        .wr-project-stats { font-size: 12px; color: #6b7280; }
        .dark .wr-project-stats { color: #9ca3af; }
        .wr-progress-bar { width: 80px; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; }
        .dark .wr-progress-bar { background: #374151; }
        .wr-progress-fill { height: 100%; background: #22c55e; border-radius: 3px; transition: width 0.3s; }
        .wr-progress-text { font-size: 12px; font-weight: 600; color: #22c55e; min-width: 40px; text-align: right; }

        .wr-performer-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .dark .wr-performer-item { border-bottom-color: #374151; }
        .wr-performer-item:last-child { border-bottom: none; }
        .wr-performer-rank { width: 24px; height: 24px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: #6b7280; }
        .dark .wr-performer-rank { background: #374151; color: #9ca3af; }
        .wr-performer-rank.gold { background: #fef3c7; color: #b45309; }
        .wr-performer-rank.silver { background: #f3f4f6; color: #4b5563; }
        .wr-performer-rank.bronze { background: #fed7aa; color: #c2410c; }
        .wr-performer-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #6366f1); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: white; }
        .wr-performer-name { flex: 1; font-size: 14px; font-weight: 500; color: #1f2937; }
        .dark .wr-performer-name { color: #f3f4f6; }
        .wr-performer-count { font-size: 14px; font-weight: 600; color: #22c55e; }
        .wr-task-item { padding: 12px; background: #f9fafb; border-radius: 8px; margin-bottom: 10px; }
        .dark .wr-task-item { background: rgba(255,255,255,0.04); }
        .wr-task-title { font-size: 14px; font-weight: 500; color: #1f2937; margin-bottom: 6px; }
        .dark .wr-task-title { color: #f3f4f6; }
        .wr-task-meta { display: flex; align-items: center; gap: 12px; font-size: 12px; color: #6b7280; }
        .dark .wr-task-meta { color: #9ca3af; }
        .wr-priority { padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; text-transform: uppercase; }
        .wr-priority.high { background: rgba(239,68,68,0.1); color: #dc2626; }
        .wr-priority.medium { background: rgba(245,158,11,0.1); color: #d97706; }
        .wr-priority.low { background: rgba(34,197,94,0.1); color: #16a34a; }
        .wr-status { padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .wr-status.to_do { background: rgba(251,191,36,0.15); color: #b45309; }
        .wr-status.in_progress { background: rgba(59,130,246,0.15); color: #1d4ed8; }
        .wr-status.done { background: rgba(34,197,94,0.15); color: #15803d; }
        .wr-empty { text-align: center; padding: 30px; color: #9ca3af; font-size: 14px; }
        .wr-standup-meter { display: flex; align-items: center; gap: 16px; padding: 16px; background: #f9fafb; border-radius: 10px; }
        .dark .wr-standup-meter { background: rgba(255,255,255,0.04); }
        .wr-standup-circle { width: 60px; height: 60px; border-radius: 50%; background: conic-gradient(#22c55e calc(var(--percent) * 1%), #e5e7eb 0); display: flex; align-items: center; justify-content: center; }
        .wr-standup-inner { width: 48px; height: 48px; border-radius: 50%; background: #f9fafb; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: #1f2937; }
        .dark .wr-standup-inner { background: #1f2937; color: #f9fafb; }
        .wr-standup-info { flex: 1; }
        .wr-standup-label { font-size: 14px; font-weight: 500; color: #1f2937; }
        .dark .wr-standup-label { color: #f3f4f6; }
        .wr-standup-detail { font-size: 12px; color: #6b7280; }
        .dark .wr-standup-detail { color: #9ca3af; }
    </style>

    <div class="wr-container">
        {{-- Header --}}
        <div class="wr-header">
            <div>
                <div class="wr-title">Weekly Report</div>
                <div class="wr-week-label">{{ $this->getWeekLabel() }}</div>
            </div>
            <div class="wr-nav">
                <button wire:click="previousWeek" class="wr-nav-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <button wire:click="currentWeek" class="wr-current-btn" {{ $this->isCurrentWeek() ? 'disabled' : '' }}>
                    This Week
                </button>
                <button wire:click="nextWeek" class="wr-nav-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="wr-stats-grid">
            <div class="wr-stat-card">
                <div class="wr-stat-header">
                    <div class="wr-stat-icon success">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="wr-stat-value">
                    {{ $summary['completed'] ?? 0 }}
                    @if(($summary['completed_change'] ?? 0) != 0)
                        <span class="wr-stat-change {{ ($summary['completed_change'] ?? 0) > 0 ? 'up' : 'down' }}">
                            {{ ($summary['completed_change'] ?? 0) > 0 ? '+' : '' }}{{ $summary['completed_change'] ?? 0 }}
                        </span>
                    @endif
                </div>
                <div class="wr-stat-label">Tasks Completed</div>
            </div>
            <div class="wr-stat-card">
                <div class="wr-stat-header">
                    <div class="wr-stat-icon info">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="wr-stat-value">{{ $summary['created'] ?? 0 }}</div>
                <div class="wr-stat-label">Tasks Created</div>
            </div>
            <div class="wr-stat-card">
                <div class="wr-stat-header">
                    <div class="wr-stat-icon warning">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="wr-stat-value">{{ $summary['in_progress'] ?? 0 }}</div>
                <div class="wr-stat-label">In Progress</div>
            </div>
            <div class="wr-stat-card">
                <div class="wr-stat-header">
                    <div class="wr-stat-icon danger">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <div class="wr-stat-value" style="color: {{ ($summary['overdue'] ?? 0) > 0 ? '#ef4444' : '#22c55e' }}">{{ $summary['overdue'] ?? 0 }}</div>
                <div class="wr-stat-label">Overdue Tasks</div>
            </div>
        </div>

        {{-- Sections --}}
        <div class="wr-sections">
            {{-- Projects Progress --}}
            <div class="wr-section">
                <div class="wr-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3.75 3A1.75 1.75 0 002 4.75v10.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0018 15.25v-8.5A1.75 1.75 0 0016.25 5h-4.836a.25.25 0 01-.177-.073L9.823 3.513A1.75 1.75 0 008.586 3H3.75z"/>
                    </svg>
                    Project Activity
                </div>
                @forelse($tasksByProject as $project)
                    <div class="wr-project-item">
                        <div class="wr-project-info">
                            <div class="wr-project-name">{{ $project['name'] }}</div>
                            <div class="wr-project-stats">{{ $project['completed'] }} completed · {{ $project['active'] }} active</div>
                        </div>
                        <div class="wr-progress-bar">
                            <div class="wr-progress-fill" style="width: {{ $project['progress'] }}%"></div>
                        </div>
                        <div class="wr-progress-text">{{ $project['progress'] }}%</div>
                    </div>
                @empty
                    <div class="wr-empty">No project activity this week</div>
                @endforelse
            </div>

            {{-- Top Performers --}}
            @if(count($topPerformers) > 0)
            <div class="wr-section">
                <div class="wr-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 1c-1.828 0-3.623.149-5.371.435a.75.75 0 00-.629.74v.387c-.827.157-1.642.345-2.445.564a.75.75 0 00-.552.698 5 5 0 004.503 5.152 6 6 0 002.946 1.822A6.451 6.451 0 017.768 13H7.5A1.5 1.5 0 006 14.5V17h-.75C4.56 17 4 17.56 4 18.25c0 .414.336.75.75.75h10.5a.75.75 0 00.75-.75c0-.69-.56-1.25-1.25-1.25H14v-2.5a1.5 1.5 0 00-1.5-1.5h-.268a6.453 6.453 0 01-.684-2.202 6 6 0 002.946-1.822 5 5 0 004.503-5.152.75.75 0 00-.552-.698A31.804 31.804 0 0016 2.562v-.387a.75.75 0 00-.629-.74A33.227 33.227 0 0010 1zM2.525 4.422C3.012 4.3 3.504 4.19 4 4.09V5c0 .74.134 1.448.38 2.103a3.503 3.503 0 01-1.855-2.68zm14.95 0a3.503 3.503 0 01-1.854 2.68C15.866 6.449 16 5.74 16 5v-.91c.496.099.988.21 1.475.332z" clip-rule="evenodd"/>
                    </svg>
                    Top Performers
                </div>
                @foreach($topPerformers as $index => $performer)
                    <div class="wr-performer-item">
                        <div class="wr-performer-rank {{ $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : '')) }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="wr-performer-avatar">{{ $performer['initials'] }}</div>
                        <div class="wr-performer-name">{{ $performer['name'] }}</div>
                        <div class="wr-performer-count">{{ $performer['completed'] }} tasks</div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Completed Tasks --}}
            <div class="wr-section">
                <div class="wr-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                    Completed This Week
                </div>
                @forelse(array_slice($completedTasks, 0, 5) as $task)
                    <div class="wr-task-item">
                        <div class="wr-task-title">{{ $task['title'] }}</div>
                        <div class="wr-task-meta">
                            <span>{{ $task['project'] }}</span>
                            <span>·</span>
                            <span>{{ $task['assignee'] }}</span>
                            <span class="wr-priority {{ $task['priority'] }}">{{ $task['priority'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="wr-empty">No tasks completed this week</div>
                @endforelse
            </div>

            {{-- New Tasks --}}
            <div class="wr-section">
                <div class="wr-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                    </svg>
                    New Tasks This Week
                </div>
                @forelse(array_slice($newTasks, 0, 5) as $task)
                    <div class="wr-task-item">
                        <div class="wr-task-title">{{ $task['title'] }}</div>
                        <div class="wr-task-meta">
                            <span>{{ $task['project'] }}</span>
                            <span>·</span>
                            <span>{{ $task['assignee'] }}</span>
                            <span class="wr-status {{ $task['status'] }}">{{ str_replace('_', ' ', $task['status']) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="wr-empty">No new tasks this week</div>
                @endforelse
            </div>

            {{-- Standup Stats --}}
            @if(count($standupStats) > 0)
            <div class="wr-section">
                <div class="wr-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd"/>
                    </svg>
                    Standup Participation
                </div>
                <div class="wr-standup-meter">
                    <div class="wr-standup-circle" style="--percent: {{ $standupStats['rate'] ?? 0 }}">
                        <div class="wr-standup-inner">{{ $standupStats['rate'] ?? 0 }}%</div>
                    </div>
                    <div class="wr-standup-info">
                        <div class="wr-standup-label">Team Submission Rate</div>
                        <div class="wr-standup-detail">{{ $standupStats['submitted'] ?? 0 }} of {{ $standupStats['expected'] ?? 0 }} expected standups</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
