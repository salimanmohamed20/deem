@php
    $announcements = \App\Models\Announcement::active()
        ->orderByDesc('is_pinned')
        ->orderByDesc('published_at')
        ->limit(3)
        ->get();
@endphp

@if($announcements->count() > 0)
<div class="mt-auto border-t border-gray-200 dark:border-gray-700">
    @foreach($announcements as $announcement)
        @php
            $styles = [
                'info' => [
                    'bg' => 'bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20',
                    'border' => 'border-l-4 border-amber-400',
                    'icon_bg' => 'bg-amber-100 dark:bg-amber-800/50',
                    'icon' => 'text-amber-600 dark:text-amber-400',
                    'title' => 'text-amber-700 dark:text-amber-300',
                ],
                'warning' => [
                    'bg' => 'bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20',
                    'border' => 'border-l-4 border-yellow-500',
                    'icon_bg' => 'bg-yellow-100 dark:bg-yellow-800/50',
                    'icon' => 'text-yellow-600 dark:text-yellow-400',
                    'title' => 'text-yellow-700 dark:text-yellow-300',
                ],
                'success' => [
                    'bg' => 'bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20',
                    'border' => 'border-l-4 border-emerald-500',
                    'icon_bg' => 'bg-emerald-100 dark:bg-emerald-800/50',
                    'icon' => 'text-emerald-600 dark:text-emerald-400',
                    'title' => 'text-emerald-700 dark:text-emerald-300',
                ],
                'danger' => [
                    'bg' => 'bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20',
                    'border' => 'border-l-4 border-red-500',
                    'icon_bg' => 'bg-red-100 dark:bg-red-800/50',
                    'icon' => 'text-red-600 dark:text-red-400',
                    'title' => 'text-red-700 dark:text-red-300',
                ],
            ];
            $style = $styles[$announcement->type] ?? $styles['info'];
        @endphp
        
        <div class="p-4 {{ $style['bg'] }} {{ $style['border'] }}">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full {{ $style['icon_bg'] }} flex items-center justify-center">
                        <x-heroicon-o-information-circle class="w-5 h-5 {{ $style['icon'] }}" />
                    </div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold {{ $style['title'] }} leading-snug">
                        {{ $announcement->title }}
                    </h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $announcement->published_at?->format('M d, Y, h:i A') ?? $announcement->created_at->format('M d, Y, h:i A') }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 leading-relaxed">
                        {!! Str::limit(strip_tags($announcement->content), 150) !!}
                    </p>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif
