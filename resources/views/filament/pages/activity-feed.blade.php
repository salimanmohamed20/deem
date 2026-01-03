<x-filament-panels::page>
    <style>
        .af-container { display: flex; flex-direction: column; gap: 20px; }
        .af-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .af-title { font-size: 14px; color: #6b7280; }
        .dark .af-title { color: #9ca3af; }
        .af-filters { display: flex; gap: 8px; flex-wrap: wrap; }
        .af-filter-btn { padding: 8px 16px; border-radius: 20px; border: 1px solid #e5e7eb; background: white; color: #6b7280; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.15s; }
        .dark .af-filter-btn { background: #374151; border-color: #4b5563; color: #9ca3af; }
        .af-filter-btn:hover { border-color: #3b82f6; color: #3b82f6; }
        .af-filter-btn.active { background: #3b82f6; border-color: #3b82f6; color: white; }
        .af-feed { background: white; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); overflow: hidden; }
        .dark .af-feed { background: #1f2937; border-color: rgba(255,255,255,0.05); }

        .af-item { display: flex; gap: 16px; padding: 16px 20px; border-bottom: 1px solid #f3f4f6; transition: background 0.15s; }
        .dark .af-item { border-bottom-color: #374151; }
        .af-item:hover { background: #f9fafb; }
        .dark .af-item:hover { background: rgba(255,255,255,0.02); }
        .af-item:last-child { border-bottom: none; }
        .af-icon-wrap { position: relative; }
        .af-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .af-icon svg { width: 18px; height: 18px; }
        .af-icon.success { background: rgba(34,197,94,0.1); color: #22c55e; }
        .af-icon.info { background: rgba(59,130,246,0.1); color: #3b82f6; }
        .af-icon.warning { background: rgba(245,158,11,0.1); color: #f59e0b; }
        .af-icon.danger { background: rgba(239,68,68,0.1); color: #ef4444; }
        .af-icon.gray { background: rgba(107,114,128,0.1); color: #6b7280; }
        .af-icon.primary { background: rgba(139,92,246,0.1); color: #8b5cf6; }
        .af-content { flex: 1; min-width: 0; }
        .af-content-header { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .af-type { font-size: 13px; font-weight: 600; color: #1f2937; }
        .dark .af-type { color: #f3f4f6; }
        .af-meta { font-size: 12px; color: #9ca3af; background: #f3f4f6; padding: 2px 8px; border-radius: 10px; }
        .dark .af-meta { background: #374151; }
        .af-description { font-size: 14px; color: #4b5563; margin-bottom: 6px; }
        .dark .af-description { color: #d1d5db; }
        .af-description a { color: #3b82f6; text-decoration: none; }
        .af-description a:hover { text-decoration: underline; }
        .af-footer { display: flex; align-items: center; gap: 12px; }
        .af-user { display: flex; align-items: center; gap: 6px; }
        .af-avatar { width: 20px; height: 20px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #6366f1); display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 600; color: white; }
        .af-username { font-size: 12px; color: #6b7280; }
        .dark .af-username { color: #9ca3af; }
        .af-time { font-size: 12px; color: #9ca3af; }
        .af-load-more { text-align: center; padding: 16px; }
        .af-load-btn { padding: 10px 24px; border-radius: 8px; border: 1px solid #e5e7eb; background: white; color: #374151; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.15s; }
        .dark .af-load-btn { background: #374151; border-color: #4b5563; color: #f3f4f6; }
        .af-load-btn:hover { background: #f3f4f6; }
        .dark .af-load-btn:hover { background: #4b5563; }
        .af-empty { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .af-empty svg { width: 48px; height: 48px; margin: 0 auto 16px; opacity: 0.5; }
        .af-date-divider { padding: 8px 20px; background: #f9fafb; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        .dark .af-date-divider { background: #111827; color: #9ca3af; }
    </style>

    <div class="af-container" wire:poll.60s="loadActivities">
        {{-- Header --}}
        <div class="af-header">
            <div class="af-title">Recent activity from the last 7 days</div>
            <div class="af-filters">
                @foreach($this->getFilterOptions() as $value => $label)
                    <button wire:click="$set('filter', '{{ $value }}')" 
                            class="af-filter-btn {{ $filter === $value ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Feed --}}
        <div class="af-feed">
            @php $lastDate = null; @endphp
            @forelse($activities as $activity)
                @php
                    $activityDate = $activity['timestamp']->format('Y-m-d');
                    $showDivider = $lastDate !== $activityDate;
                    $lastDate = $activityDate;
                @endphp
                
                @if($showDivider)
                    <div class="af-date-divider">
                        @if($activity['timestamp']->isToday())
                            Today
                        @elseif($activity['timestamp']->isYesterday())
                            Yesterday
                        @else
                            {{ $activity['timestamp']->format('l, M j') }}
                        @endif
                    </div>
                @endif

                <div class="af-item">
                    <div class="af-icon-wrap">
                        <div class="af-icon {{ $activity['color'] }}">
                            @if($activity['icon'] === 'check-circle')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($activity['icon'] === 'plus-circle')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($activity['icon'] === 'play')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($activity['icon'] === 'chat-bubble-left')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 2c-2.236 0-4.43.18-6.57.524C1.993 2.755 1 4.014 1 5.426v5.148c0 1.413.993 2.67 2.43 2.902.848.137 1.705.248 2.57.331v3.443a.75.75 0 001.28.53l3.58-3.579a.78.78 0 01.527-.224 41.202 41.202 0 005.183-.5c1.437-.232 2.43-1.49 2.43-2.903V5.426c0-1.413-.993-2.67-2.43-2.902A41.289 41.289 0 0010 2zm0 7a1 1 0 100-2 1 1 0 000 2zM8 8a1 1 0 11-2 0 1 1 0 012 0zm5 1a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($activity['icon'] === 'clipboard-document-check')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 5.25a2.25 2.25 0 00-2.012-2.238A2.25 2.25 0 0013.75 1h-1.5a2.25 2.25 0 00-2.238 2.012c-.875.092-1.6.686-1.884 1.488H11A2.5 2.5 0 0113.5 7v7h2.25A2.25 2.25 0 0018 11.75v-6.5zM12.25 2.5a.75.75 0 00-.75.75v.25h3v-.25a.75.75 0 00-.75-.75h-1.5z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M3 6a1 1 0 00-1 1v10a1 1 0 001 1h8a1 1 0 001-1V7a1 1 0 00-1-1H3zm6.874 4.166a.75.75 0 10-1.248-.832l-2.493 3.739-1.093-1.093a.75.75 0 10-1.06 1.06l1.75 1.75a.75.75 0 001.154-.114l3-4.5z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </div>
                    <div class="af-content">
                        <div class="af-content-header">
                            <span class="af-type">{{ $activity['title'] }}</span>
                            @if($activity['meta'])
                                <span class="af-meta">{{ $activity['meta'] }}</span>
                            @endif
                        </div>
                        <div class="af-description">
                            <a href="{{ $activity['link'] }}">{{ $activity['description'] }}</a>
                        </div>
                        <div class="af-footer">
                            <div class="af-user">
                                <div class="af-avatar">{{ $activity['user_initials'] }}</div>
                                <span class="af-username">{{ $activity['user'] }}</span>
                            </div>
                            <span class="af-time">{{ $activity['timestamp']->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="af-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <div style="font-size: 16px; font-weight: 500;">No activity yet</div>
                    <div style="font-size: 14px; margin-top: 4px;">Activity will appear here as your team works</div>
                </div>
            @endforelse
        </div>

        @if($activities->count() >= $limit)
            <div class="af-load-more">
                <button wire:click="loadMore" class="af-load-btn">Load More</button>
            </div>
        @endif
    </div>
</x-filament-panels::page>
