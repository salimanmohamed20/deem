@php
    $announcements = \App\Models\Announcement::active()
        ->orderByDesc('is_pinned')
        ->orderByDesc('published_at')
        ->limit(3)
        ->get();
@endphp

@if($announcements->count() > 0)
    <div class="px-3 py-4 border-t border-gray-200 dark:border-white/10">
        <div class="space-y-3">
            @foreach($announcements as $announcement)
                @php
                    $iconColors = [
                        'info' => 'text-blue-500 bg-blue-100 dark:bg-blue-900/50',
                        'warning' => 'text-amber-500 bg-amber-100 dark:bg-amber-900/50',
                        'success' => 'text-emerald-500 bg-emerald-100 dark:bg-emerald-900/50',
                        'danger' => 'text-red-500 bg-red-100 dark:bg-red-900/50',
                    ];
                    $titleColors = [
                        'info' => 'text-blue-700 dark:text-blue-300',
                        'warning' => 'text-amber-700 dark:text-amber-300',
                        'success' => 'text-emerald-700 dark:text-emerald-300',
                        'danger' => 'text-red-700 dark:text-red-300',
                    ];
                @endphp
                
                <div class="flex gap-3">
                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $iconColors[$announcement->type] ?? $iconColors['info'] }}">
                            @switch($announcement->type)
                                @case('warning')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                    </svg>
                                    @break
                                @case('success')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                    </svg>
                                    @break
                                @case('danger')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                                    </svg>
                                    @break
                                @default
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                                    </svg>
                            @endswitch
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold {{ $titleColors[$announcement->type] ?? $titleColors['info'] }} leading-tight">
                            {{ $announcement->title }}
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $announcement->published_at?->format('M d, Y, h:i A') ?? $announcement->created_at->format('M d, Y, h:i A') }}
                        </p>
                        <div class="text-xs text-gray-600 dark:text-gray-300 mt-1.5 line-clamp-3 prose-sm">
                            {!! Str::limit(strip_tags($announcement->content), 120) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
