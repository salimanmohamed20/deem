<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Traits\HasRoleBasedAccess;
use Filament\Widgets\ChartWidget;

class ProjectProgressWidget extends ChartWidget
{
    use HasRoleBasedAccess;

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return self::isEmployee() ? 'My Projects' : 'Project Progress';
    }

    public function getDescription(): ?string
    {
        return 'Completion percentage by project';
    }

    public function getMaxHeight(): ?string
    {
        return '280px';
    }

    protected function getData(): array
    {
        $projects = Project::query()
            ->forCurrentEmployee()
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('status', 'done');
                },
            ])
            ->having('tasks_count', '>', 0)
            ->orderByDesc('tasks_count')
            ->limit(5)
            ->get();

        $labels = $projects->pluck('name')->map(function ($name) {
            return strlen($name) > 15 ? substr($name, 0, 15) . '...' : $name;
        })->toArray();
        
        $progress = $projects->map(function ($project) {
            return $project->tasks_count > 0 
                ? round(($project->completed_tasks_count / $project->tasks_count) * 100) 
                : 0;
        })->toArray();

        $colors = [
            'rgb(99, 102, 241)',
            'rgb(139, 92, 246)',
            'rgb(236, 72, 153)',
            'rgb(244, 114, 182)',
            'rgb(251, 146, 60)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Progress',
                    'data' => $progress,
                    'backgroundColor' => array_slice($colors, 0, count($progress)),
                    'borderWidth' => 0,
                    'borderRadius' => 4,
                    'barThickness' => 24,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'max' => 100,
                    'grid' => [
                        'display' => true,
                        'color' => 'rgba(0, 0, 0, 0.05)',
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return value + "%"; }',
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'maintainAspectRatio' => true,
        ];
    }
}
