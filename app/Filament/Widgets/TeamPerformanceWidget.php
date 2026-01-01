<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TeamPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Single optimized query
        $stats = DB::table('tasks')
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "done" THEN 1 ELSE 0 END) as completed')
            ->first();

        $projectsCount = DB::table('projects')->count();
        $tasksCount = $stats->total ?? 0;
        $completedTasks = $stats->completed ?? 0;
        $completionPercentage = $tasksCount > 0 ? round(($completedTasks / $tasksCount) * 100, 1) : 0;

        return [
            Stat::make('Total Projects', $projectsCount)
                ->description('Active projects')
                ->color('primary'),
            Stat::make('Total Tasks', $tasksCount)
                ->description('All tasks')
                ->color('info'),
            Stat::make('Completion Rate', $completionPercentage . '%')
                ->description($completedTasks . ' completed')
                ->color($completionPercentage >= 70 ? 'success' : 'warning'),
        ];
    }
}
