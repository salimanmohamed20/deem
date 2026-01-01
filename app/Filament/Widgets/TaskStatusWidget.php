<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TaskStatusWidget extends ChartWidget
{
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return 'Tasks by Status';
    }

    public function getMaxHeight(): ?string
    {
        return '250px';
    }

    protected function getData(): array
    {
        $todo = Task::where('status', 'to_do')->count();
        $inProgress = Task::where('status', 'in_progress')->count();
        $done = Task::where('status', 'done')->count();

        return [
            'datasets' => [
                [
                    'data' => [$todo, $inProgress, $done],
                    'backgroundColor' => [
                        'rgba(251, 191, 36, 0.9)',
                        'rgba(59, 130, 246, 0.9)',
                        'rgba(34, 197, 94, 0.9)',
                    ],
                    'borderWidth' => 0,
                    'hoverOffset' => 10,
                ],
            ],
            'labels' => ['To Do', 'In Progress', 'Done'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
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
        ];
    }
}
