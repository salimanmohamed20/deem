<x-filament-panels::page>
    <style>
        .tw-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .tw-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .tw-filter {
            min-width: 200px;
        }

        .tw-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .tw-stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dark .tw-stat-card {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .tw-stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
        }

        .dark .tw-stat-value {
            color: #f9fafb;
        }

        .tw-stat-label {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        .dark .tw-stat-label {
            color: #9ca3af;
        }

        .tw-stat-card.warning .tw-stat-value { color: #f59e0b; }
        .tw-stat-card.danger .tw-stat-value { color: #ef4444; }
        .tw-stat-card.success .tw-stat-value { color: #22c55e; }
        .tw-stat-card.info .tw-stat-value { color: #3b82f6; }

        .tw-team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .tw-member-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }

        .dark .tw-member-card {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .tw-member-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .tw-member-card.overloaded {
            border-left: 4px solid #ef4444;
        }

        .tw-member-card.balanced {
            border-left: 4px solid #22c55e;
        }

        .tw-member-card.light {
            border-left: 4px solid #3b82f6;
        }

        .tw-member-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .tw-member-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .tw-member-info {
            flex: 1;
            min-width: 0;
        }

        .tw-member-name {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dark .tw-member-name {
            color: #f9fafb;
        }

        .tw-member-title {
            font-size: 13px;
            color: #6b7280;
        }

        .dark .tw-member-title {
            color: #9ca3af;
        }

        .tw-workload-indicator {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .tw-workload-score {
            font-size: 24px;
            font-weight: 700;
        }

        .tw-workload-score.high { color: #ef4444; }
        .tw-workload-score.medium { color: #f59e0b; }
        .tw-workload-score.low { color: #22c55e; }

        .tw-workload-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
        }

        .tw-task-bars {
            margin-bottom: 16px;
        }

        .tw-bar-container {
            height: 8px;
            background: #f3f4f6;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
        }

        .dark .tw-bar-container {
            background: rgba(255, 255, 255, 0.1);
        }

        .tw-bar-segment {
            height: 100%;
            transition: width 0.3s ease;
        }

        .tw-bar-segment.todo { background: #fbbf24; }
        .tw-bar-segment.in-progress { background: #3b82f6; }
        .tw-bar-segment.done { background: #22c55e; }

        .tw-task-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .tw-task-stat {
            text-align: center;
            padding: 10px 8px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .dark .tw-task-stat {
            background: rgba(255, 255, 255, 0.04);
        }

        .tw-task-stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .dark .tw-task-stat-value {
            color: #f9fafb;
        }

        .tw-task-stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .dark .tw-task-stat-label {
            color: #9ca3af;
        }

        .tw-task-stat.warning .tw-task-stat-value { color: #f59e0b; }
        .tw-task-stat.danger .tw-task-stat-value { color: #ef4444; }
        .tw-task-stat.success .tw-task-stat-value { color: #22c55e; }
        .tw-task-stat.info .tw-task-stat-value { color: #3b82f6; }

        .tw-alerts {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .tw-alert-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .tw-alert-badge.overdue {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .tw-alert-badge.high-priority {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .dark .tw-alert-badge.overdue {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .dark .tw-alert-badge.high-priority {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        .tw-empty {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .tw-empty svg {
            width: 48px;
            height: 48px;
            margin: 0 auto 16px;
            opacity: 0.5;
        }

        .tw-legend {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-top: 8px;
        }

        .tw-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #6b7280;
        }

        .dark .tw-legend-item {
            color: #9ca3af;
        }

        .tw-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
        }

        .tw-legend-dot.todo { background: #fbbf24; }
        .tw-legend-dot.in-progress { background: #3b82f6; }
        .tw-legend-dot.done { background: #22c55e; }
    </style>

    <div class="tw-container" wire:poll.30s="loadWorkload">
        {{-- Header with Filter --}}
        <div class="tw-header">
            <div>
                <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin: 0;">
                    Team Workload Distribution
                </h2>
                <p style="font-size: 14px; color: #6b7280; margin-top: 4px;">
                    Monitor task distribution and identify bottlenecks
                </p>
            </div>
            <div class="tw-filter">
                <select wire:model.live="selectedProject" 
                        class="fi-select-input block w-full rounded-lg border-gray-300 shadow-sm transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach($this->getProjectOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Team Stats --}}
        <div class="tw-stats-grid">
            <div class="tw-stat-card info">
                <div class="tw-stat-value">{{ $workloadStats['team_size'] ?? 0 }}</div>
                <div class="tw-stat-label">Team Members</div>
            </div>
            <div class="tw-stat-card warning">
                <div class="tw-stat-value">{{ $workloadStats['total_active_tasks'] ?? 0 }}</div>
                <div class="tw-stat-label">Active Tasks</div>
            </div>
            <div class="tw-stat-card {{ ($workloadStats['total_overdue'] ?? 0) > 0 ? 'danger' : 'success' }}">
                <div class="tw-stat-value">{{ $workloadStats['total_overdue'] ?? 0 }}</div>
                <div class="tw-stat-label">Overdue Tasks</div>
            </div>
            <div class="tw-stat-card">
                <div class="tw-stat-value">{{ $workloadStats['avg_workload'] ?? 0 }}</div>
                <div class="tw-stat-label">Avg Workload Score</div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="tw-legend">
            <div class="tw-legend-item">
                <div class="tw-legend-dot todo"></div>
                To Do
            </div>
            <div class="tw-legend-item">
                <div class="tw-legend-dot in-progress"></div>
                In Progress
            </div>
            <div class="tw-legend-item">
                <div class="tw-legend-dot done"></div>
                Done
            </div>
        </div>

        {{-- Team Members Grid --}}
        @if($teamMembers->count() > 0)
            <div class="tw-team-grid">
                @foreach($teamMembers as $member)
                    @php
                        $workloadLevel = match(true) {
                            $member['workload_score'] >= 15 => 'overloaded',
                            $member['workload_score'] >= 8 => 'balanced',
                            default => 'light',
                        };
                        $scoreClass = match(true) {
                            $member['workload_score'] >= 15 => 'high',
                            $member['workload_score'] >= 8 => 'medium',
                            default => 'low',
                        };
                        $total = $member['todo'] + $member['in_progress'] + $member['done'];
                        $todoPercent = $total > 0 ? ($member['todo'] / $total) * 100 : 0;
                        $progressPercent = $total > 0 ? ($member['in_progress'] / $total) * 100 : 0;
                        $donePercent = $total > 0 ? ($member['done'] / $total) * 100 : 0;
                    @endphp
                    <div class="tw-member-card {{ $workloadLevel }}">
                        <div class="tw-member-header">
                            <div class="tw-member-avatar">{{ $member['avatar'] }}</div>
                            <div class="tw-member-info">
                                <div class="tw-member-name">{{ $member['name'] }}</div>
                                <div class="tw-member-title">{{ $member['job_title'] }}</div>
                            </div>
                            <div class="tw-workload-indicator">
                                <div class="tw-workload-score {{ $scoreClass }}">{{ $member['workload_score'] }}</div>
                                <div class="tw-workload-label">Workload</div>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="tw-task-bars">
                            <div class="tw-bar-container">
                                @if($total > 0)
                                    <div class="tw-bar-segment todo" style="width: {{ $todoPercent }}%"></div>
                                    <div class="tw-bar-segment in-progress" style="width: {{ $progressPercent }}%"></div>
                                    <div class="tw-bar-segment done" style="width: {{ $donePercent }}%"></div>
                                @endif
                            </div>
                        </div>

                        {{-- Task Stats --}}
                        <div class="tw-task-stats">
                            <div class="tw-task-stat">
                                <div class="tw-task-stat-value">{{ $member['total_tasks'] }}</div>
                                <div class="tw-task-stat-label">Total</div>
                            </div>
                            <div class="tw-task-stat warning">
                                <div class="tw-task-stat-value">{{ $member['todo'] }}</div>
                                <div class="tw-task-stat-label">To Do</div>
                            </div>
                            <div class="tw-task-stat info">
                                <div class="tw-task-stat-value">{{ $member['in_progress'] }}</div>
                                <div class="tw-task-stat-label">Active</div>
                            </div>
                            <div class="tw-task-stat success">
                                <div class="tw-task-stat-value">{{ $member['completion_rate'] }}%</div>
                                <div class="tw-task-stat-label">Done</div>
                            </div>
                        </div>

                        {{-- Alerts --}}
                        @if($member['overdue'] > 0 || $member['high_priority'] > 0)
                            <div class="tw-alerts">
                                @if($member['overdue'] > 0)
                                    <span class="tw-alert-badge overdue">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $member['overdue'] }} overdue
                                    </span>
                                @endif
                                @if($member['high_priority'] > 0)
                                    <span class="tw-alert-badge high-priority">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $member['high_priority'] }} high priority
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="tw-empty">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                </svg>
                <div style="font-size: 16px; font-weight: 500;">No team members found</div>
                <div style="font-size: 14px; margin-top: 4px;">Select a project or add team members to see workload</div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
