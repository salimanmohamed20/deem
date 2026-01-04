<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class AnnouncementsWidget extends Widget
{
    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 'full';

    public function render(): View
    {
        return view('filament.widgets.announcements-widget', [
            'announcements' => $this->getAnnouncements(),
        ]);
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
