@php
    $announcements = \App\Models\Announcement::active()
        ->orderByDesc('is_pinned')
        ->orderByDesc('published_at')
        ->limit(3)
        ->get();
@endphp

@if($announcements->count() > 0)
<div style="border-top: 1px solid #e5e7eb; padding: 16px;">
    @foreach($announcements as $announcement)
        <div style="display: flex; gap: 12px; margin-bottom: 16px;">
            {{-- Icon Circle --}}
            <div style="flex-shrink: 0;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #fef3c7; border: 2px solid #fcd34d; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 20px; height: 20px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            
            {{-- Content --}}
            <div style="flex: 1;">
                <h4 style="font-size: 14px; font-weight: 600; color: #d97706; margin: 0 0 4px 0; line-height: 1.3;">
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
