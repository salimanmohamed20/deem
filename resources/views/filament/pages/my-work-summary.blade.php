<x-filament-panels::page>
    <style>
        .mws-container {
            display: grid;
            gap: 24px;
        }

        .mws-greeting {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .dark .mws-greeting {
            color: #f9fafb;
        }

        .mws-date {
            font-size: 14px;
            color: #6b7280;
        }

        .dark .mws-date {
            color: #9ca3af;
        }

        .mws-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
        }

        .mws-stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .dark .mws-stat-card {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .mws-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .mws-stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1;
        }

        .dark .mws-stat-value {
            color: #f9fafb;
        }

        .mws-stat-label {
            font-size: 13px;
            color: #6b7280;
            margin-top: 8px;
        }

        .dark .mws-stat-label {
            color: #9ca3af;
        }

        .mws-stat-card.warning .mws-stat-value {
            color: #f59e0b;
        }

        .mws-stat-card.danger .mws-stat-value {
            color: #ef4444;
        }

        .mws-stat-card.success .mws-stat-value {
            color: #22c55e;
        }

        .mws-stat-card.info .mws-stat-value {
            color: #3b82f6;
        }

        .mws-sections {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        @media (max-width: 1024px) {
            .mws-sections {
                grid-template-columns: 1fr;
            }
        }

        .mws-section {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dark .mws-section {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .mws-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .mws-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }

        .dark .mws-section-title {
            color: #f9fafb;
        }

        .mws-section-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mws-section-icon svg {
            width: 18px;
            height: 18px;
        }

        .mws-section-icon.today {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .mws-section-icon.upcoming {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
        }

        .mws-section-icon.overdue {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .mws-section-icon.activity {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .mws-section-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .mws-section-badge.today {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .mws-section-badge.upcoming {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
        }

        .mws-section-badge.overdue {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .mws-task-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .mws-task-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px;
            background: #f9fafb;
            border-radius: 10px;
            transition: all 0.15s;
            cursor: pointer;
        }

        .dark .mws-task-item {
            background: rgba(255, 255, 255, 0.04);
        }

        .mws-task-item:hover {
            background: #f3f4f6;
            transform: translateX(4px);
        }

        .dark .mws-task-item:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .mws-task-priority {
            width: 4px;
            height: 100%;
            min-height: 40px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        .mws-task-priority.high { background: #ef4444; }
        .mws-task-priority.medium { background: #f59e0b; }
        .mws-task-priority.low { background: #22c55e; }

        .mws-task-content {
            flex: 1;
            min-width: 0;
        }

        .mws-task-title {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dark .mws-task-title {
            color: #f9fafb;
        }

        .mws-task-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            color: #6b7280;
        }

        .dark .mws-task-meta {
            color: #9ca3af;
        }

        .mws-task-meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .mws-task-meta-item svg {
            width: 12px;
            height: 12px;
        }

        .mws-task-date {
            font-size: 12px;
            color: #6b7280;
            white-space: nowrap;
        }

        .mws-task-date.overdue {
            color: #ef4444;
            font-weight: 600;
        }

        .mws-empty {
            text-align: center;
            padding: 32px 16px;
            color: #9ca3af;
        }

        .mws-empty svg {
            width: 40px;
            height: 40px;
            margin: 0 auto 12px;
            opacity: 0.5;
        }

        .mws-empty-text {
            font-size: 14px;
        }

        .mws-activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dark .mws-activity-item {
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }

        .mws-activity-item:last-child {
            border-bottom: none;
        }

        .mws-activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .mws-activity-dot.to_do { background: #f59e0b; }
        .mws-activity-dot.in_progress { background: #3b82f6; }
        .mws-activity-dot.done { background: #22c55e; }

        .mws-activity-content {
            flex: 1;
            min-width: 0;
        }

        .mws-activity-title {
            font-size: 13px;
            font-weight: 500;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dark .mws-activity-title {
            color: #f9fafb;
        }

        .mws-activity-project {
            font-size: 12px;
            color: #6b7280;
        }

        .dark .mws-activity-project {
            color: #9ca3af;
        }

        .mws-activity-time {
            font-size: 11px;
            color: #9ca3af;
            white-space: nowrap;
        }

        .mws-standup-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .dark .mws-standup-banner {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(245, 158, 11, 0.15));
        }

        .mws-standup-banner.completed {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        }

        .dark .mws-standup-banner.completed {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(22, 163, 74, 0.15));
        }

        .mws-standup-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mws-standup-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dark .mws-standup-icon {
            background: rgba(0, 0, 0, 0.2);
        }

        .mws-standup-icon svg {
            width: 20px;
            height: 20px;
            color: #f59e0b;
        }

        .mws-standup-banner.completed .mws-standup-icon svg {
            color: #22c55e;
        }

        .mws-standup-text {
            font-size: 14px;
            font-weight: 600;
            color: #92400e;
        }

        .dark .mws-standup-text {
            color: #fbbf24;
        }

        .mws-standup-banner.completed .mws-standup-text {
            color: #166534;
        }

        .dark .mws-standup-banner.completed .mws-standup-text {
            color: #4ade80;
        }

        .mws-standup-btn {
            padding: 8px 16px;
            background: white;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #92400e;
            text-decoration: none;
            transition: all 0.15s;
        }

        .dark .mws-standup-btn {
            background: rgba(0, 0, 0, 0.3);
            color: #fbbf24;
        }

        .mws-standup-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>

    <div class="mws-container">
        {{-- Standup Banner --}}
        <div class="mws-standup-banner {{ $hasStandupToday ? 'completed' : '' }}">
            <div class="mws-standup-content">
                <div class="mws-standup-icon">
                    @if($hasStandupToday)
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </div>
                <span class="mws-standup-text">
                    @if($hasStandupToday)
                        ✓ Today's standup submitted
                    @else
                        Don't forget to submit your daily standup!
                    @endif
                </span>
            </div>
            @if(!$hasStandupToday)
                <a href="{{ route('filament.admin.resources.standups.create') }}" class="mws-standup-btn">
                    Submit Standup
                </a>
            @endif
        </div>

        {{-- Stats Grid --}}
        <div class="mws-stats-grid">
            <div class="mws-stat-card info">
                <div class="mws-stat-value">{{ $stats['total'] ?? 0 }}</div>
                <div class="mws-stat-label">Total Tasks</div>
            </div>
            <div class="mws-stat-card warning">
                <div class="mws-stat-value">{{ $stats['in_progress'] ?? 0 }}</div>
                <div class="mws-stat-label">In Progress</div>
            </div>
            <div class="mws-stat-card {{ ($stats['overdue_count'] ?? 0) > 0 ? 'danger' : 'success' }}">
                <div class="mws-stat-value">{{ $stats['overdue_count'] ?? 0 }}</div>
                <div class="mws-stat-label">Overdue</div>
            </div>
            <div class="mws-stat-card success">
                <div class="mws-stat-value">{{ $stats['completed_this_week'] ?? 0 }}</div>
                <div class="mws-stat-label">Done This Week</div>
            </div>
            <div class="mws-stat-card">
                <div class="mws-stat-value">{{ $stats['completion_rate'] ?? 0 }}%</div>
                <div class="mws-stat-label">Completion Rate</div>
            </div>
        </div>

        {{-- Task Sections --}}
        <div class="mws-sections">
            {{-- Today's Tasks --}}
            <div class="mws-section">
                <div class="mws-section-header">
                    <div class="mws-section-title">
                        <div class="mws-section-icon today">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z"/>
                            </svg>
                        </div>
                        Today's Tasks
                    </div>
                    <span class="mws-section-badge today">{{ count($todayTasks) }}</span>
                </div>
                <div class="mws-task-list">
                    @forelse($todayTasks as $task)
                        <a href="{{ route('filament.admin.resources.tasks.edit', $task['id']) }}" class="mws-task-item">
                            <div class="mws-task-priority {{ $task['priority'] }}"></div>
                            <div class="mws-task-content">
                                <div class="mws-task-title">{{ $task['title'] }}</div>
                                <div class="mws-task-meta">
                                    <span class="mws-task-meta-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M3.75 3A1.75 1.75 0 002 4.75v10.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0018 15.25v-8.5A1.75 1.75 0 0016.25 5h-4.836a.25.25 0 01-.177-.073L9.823 3.513A1.75 1.75 0 008.586 3H3.75z"/>
                                        </svg>
                                        {{ $task['project']['name'] ?? 'No Project' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="mws-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="mws-empty-text">No tasks due today</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Overdue Tasks --}}
            <div class="mws-section">
                <div class="mws-section-header">
                    <div class="mws-section-title">
                        <div class="mws-section-icon overdue">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        Overdue
                    </div>
                    <span class="mws-section-badge overdue">{{ count($overdueTasks) }}</span>
                </div>
                <div class="mws-task-list">
                    @forelse($overdueTasks as $task)
                        <a href="{{ route('filament.admin.resources.tasks.edit', $task['id']) }}" class="mws-task-item">
                            <div class="mws-task-priority {{ $task['priority'] }}"></div>
                            <div class="mws-task-content">
                                <div class="mws-task-title">{{ $task['title'] }}</div>
                                <div class="mws-task-meta">
                                    <span class="mws-task-meta-item">
                                        {{ $task['project']['name'] ?? 'No Project' }}
                                    </span>
                                </div>
                            </div>
                            <span class="mws-task-date overdue">
                                {{ \Carbon\Carbon::parse($task['deadline'])->diffForHumans() }}
                            </span>
                        </a>
                    @empty
                        <div class="mws-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="mws-empty-text">No overdue tasks!</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Upcoming Tasks --}}
            <div class="mws-section">
                <div class="mws-section-header">
                    <div class="mws-section-title">
                        <div class="mws-section-icon upcoming">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        Coming Up
                    </div>
                    <span class="mws-section-badge upcoming">{{ count($upcomingTasks) }}</span>
                </div>
                <div class="mws-task-list">
                    @forelse($upcomingTasks as $task)
                        <a href="{{ route('filament.admin.resources.tasks.edit', $task['id']) }}" class="mws-task-item">
                            <div class="mws-task-priority {{ $task['priority'] }}"></div>
                            <div class="mws-task-content">
                                <div class="mws-task-title">{{ $task['title'] }}</div>
                                <div class="mws-task-meta">
                                    <span class="mws-task-meta-item">
                                        {{ $task['project']['name'] ?? 'No Project' }}
                                    </span>
                                </div>
                            </div>
                            <span class="mws-task-date">
                                {{ \Carbon\Carbon::parse($task['deadline'])->format('M j') }}
                            </span>
                        </a>
                    @empty
                        <div class="mws-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                            </svg>
                            <div class="mws-empty-text">No upcoming deadlines</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="mws-section">
                <div class="mws-section-header">
                    <div class="mws-section-title">
                        <div class="mws-section-icon activity">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        Recent Activity
                    </div>
                </div>
                <div>
                    @forelse($recentActivity as $activity)
                        <div class="mws-activity-item">
                            <div class="mws-activity-dot {{ $activity['status'] }}"></div>
                            <div class="mws-activity-content">
                                <div class="mws-activity-title">{{ $activity['title'] }}</div>
                                <div class="mws-activity-project">{{ $activity['project'] }}</div>
                            </div>
                            <span class="mws-activity-time">
                                {{ \Carbon\Carbon::parse($activity['updated_at'])->diffForHumans() }}
                            </span>
                        </div>
                    @empty
                        <div class="mws-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="mws-empty-text">No recent activity</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
