@php
    $announcements = \App\Models\Announcement::active()
        ->orderByDesc('is_pinned')
        ->orderByDesc('published_at')
        ->limit(3)
        ->get();
        
    $colors = [
        'info' => [
            'bg' => '#fef3c7',
            'border' => '#fcd34d', 
            'icon' => '#f59e0b',
            'title' => '#d97706',
        ],
        'warning' => [
            'bg' => '#fef9c3',
            'border' => '#facc15',
            'icon' => '#eab308',
            'title' => '#ca8a04',
        ],
        'success' => [
            'bg' => '#d1fae5',
            'border' => '#6ee7b7',
            'icon' => '#10b981',
            'title' => '#059669',
        ],
        'danger' => [
            'bg' => '#fee2e2',
            'border' => '#fca5a5',
            'icon' => '#ef4444',
            'title' => '#dc2626',
        ],
    ];
@endphp

@if($announcements->count() > 0)
<div style="border-top: 1px solid #e5e7eb; padding: 16px;">
    @foreach($announcements as $announcement)
        @php
            $c = $colors[$announcement->type] ?? $colors['info'];
        @endphp
        <div style="display: flex; gap: 12px; margin-bottom: 16px;">
            {{-- Icon Circle --}}
            <div style="flex-shrink: 0;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: {{ $c['bg'] }}; border: 2px solid {{ $c['border'] }}; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px; color: {{ $c['icon'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($announcement->type === 'success')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @elseif($announcement->type === 'danger')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @elseif($announcement->type === 'warning')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @endif
                    </svg>
                </div>
            </div>
            
            {{-- Content --}}
            <div style="flex: 1;">
                <h4 style="font-size: 14px; font-weight: 600; color: {{ $c['title'] }}; margin: 0 0 4px 0; line-height: 1.3;">
                    {{ $announcement->title }}
                </h4>
                <p style="font-size: 12px; color: #9ca3af; margin: 0 0 8px 0;">
                    {{ $announcement->published_at?->format('M d, Y, h:i A') ?? $announcement->created_at->format('M d, Y, h:i A') }}
                </p>
                <p style="font-size: 13px; color: #4b5563; margin: 0; line-height: 1.6;">
                    {!! Str::limit(strip_tags($announcement->content), 150) !!}
                </p>
            </div>
        </div>
    @endforeach
</div>
@endif
