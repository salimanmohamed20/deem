<x-filament-widgets::widget>
    @if($announcements->count() > 0)
        <div class="space-y-3">
            @foreach($announcements as $announcement)
                @php
                    $colors = [
                        'info' => [
                            'bg' => 'bg-blue-50 dark:bg-blue-950/50',
                            'border' => 'border-blue-200 dark:border-blue-800',
                            'icon' => 'text-blue-500',
                            'title' => 'text-blue-800 dark:text-blue-200',
                        ],
                        'warning' => [
                            'bg' => 'bg-amber-50 dark:bg-amber-950/50',
                            'border' => 'border-amber-200 dark:border-amber-800',
                            'icon' => 'text-amber-500',
                            'title' => 'text-amber-800 dark:text-amber-200',
                        ],
                        'success' => [
                            'bg' => 'bg-emerald-50 dark:bg-emerald-950/50',
                            'border' => 'border-emerald-200 dark:border-emerald-800',
                            'icon' => 'text-emerald-500',
                            'title' => 'text-emerald-800 dark:text-emerald-200',
                        ],
                        'danger' => [
                            'bg' => 'bg-red-50 dark:bg-red-950/50',
                            'border' => 'border-red-200 dark:border-red-800',
                            'icon' => 'text-red-500',
                            'title' => 'text-red-800 dark:text-red-200',
                        ],
                    ];
                    $color = $colors[$announcement->type] ?? $colors['info'];
                @endphp

                <div class="rounded-xl border {{ $color['border'] }} {{ $color['bg'] }} p-4 transition-all hover:shadow-md">
                    <div class="flex gap-4">
                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full {{ $color['bg'] }} flex items-center justify-center">
                                @if($announcement->is_pinned)
                                    <x-heroicon-s-bookmark class="w-5 h-5 {{ $color['icon'] }}" />
                                @else
                                    @switch($announcement->type)
                                        @case('warning')
                                            <x-heroicon-s-exclamation-triangle class="w-5 h-5 {{ $color['icon'] }}" />
                                            @break
                                        @case('success')
                                            <x-heroicon-s-check-circle class="w-5 h-5 {{ $color['icon'] }}" />
                                            @break
                                        @case('danger')
                                            <x-heroicon-s-x-circle class="w-5 h-5 {{ $color['icon'] }}" />
                                            @break
                                        @default
                                            <x-heroicon-s-information-circle class="w-5 h-5 {{ $color['icon'] }}" />
                                    @endswitch
                                @endif
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-semibold {{ $color['title'] }} text-base">
                                    {{ $announcement->title }}
                                </h3>
                                @if($announcement->is_pinned)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">
                                        📌 Pinned
                                    </span>
                                @endif
                            </div>
                            
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $announcement->published_at?->format('M d, Y, h:i A') ?? $announcement->created_at->format('M d, Y, h:i A') }}
                            </p>
                            
                            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300 prose prose-sm max-w-none dark:prose-invert">
                                {!! $announcement->content !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-widgets::widget>
