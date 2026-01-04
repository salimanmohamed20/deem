<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;

class AnnouncementsWidget extends Widget
{
    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 'full';

    protected function getViewName(): string
    {
        return 'filament.widgets.announcements-widget';
    }

    public function getAnnouncements()
    {
        return Announcement::active()
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();
    }
}
