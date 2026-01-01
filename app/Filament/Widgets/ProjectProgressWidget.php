<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectProgressWidget extends ChartWidget
{
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return 'Project Progress';
    }

    public function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        $projects = Project::withCount([
            'tasks',
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'done');
            },
        ])
        ->having('tasks_count', '>', 0)
        ->orderByDesc('tasks_count')
        ->limit(5)
        ->get();

        $labels = $projects->pluck('name')->toArray();
        $progress = $projects->map(function ($project) {
            return $project->tasks_count > 0 
                ? round(($project->completed_tasks_count / $project->tasks_count) * 100) 
                : 0;
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Progress %',
                    'data' => $progress,
                    'backgroundColor' => [
                        'rgba(30, 58, 95, 0.9)',
                        'rgba(59, 130, 246, 0.9)',
                        'rgba(99, 102, 241, 0.9)',
                        'rgba(139, 92, 246, 0.9)',
                        'rgba(168, 85, 247, 0.9)',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
