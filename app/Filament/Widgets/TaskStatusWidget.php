<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Traits\HasRoleBasedAccess;
use Filament\Widgets\ChartWidget;

class TaskStatusWidget extends ChartWidget
{
    use HasRoleBasedAccess;

    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return self::isEmployee() ? 'My Tasks Distribution' : 'Tasks Distribution';
    }

    public function getDescription(): ?string
    {
        return 'Current status breakdown';
    }

    public function getMaxHeight(): ?string
    {
        return '280px';
    }

    protected function getData(): array
    {
        $query = Task::query()->forCurrentEmployee();

        $todo = (clone $query)->where('status', 'to_do')->count();
        $inProgress = (clone $query)->where('status', 'in_progress')->count();
        $done = (clone $query)->where('status', 'done')->count();

        return [
            'datasets' => [
                [
                    'data' => [$todo, $inProgress, $done],
                    'backgroundColor' => [
                        'rgb(250, 204, 21)',
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                    ],
                    'borderColor' => 'transparent',
                    'borderWidth' => 0,
                    'hoverOffset' => 8,
                    'spacing' => 2,
                ],
            ],
            'labels' => ['To Do', 'In Progress', 'Done'],
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
                    'labels' => [
                        'padding' => 16,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
            ],
            'cutout' => '65%',
            'maintainAspectRatio' => true,
        ];
    }
}
