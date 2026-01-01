<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TasksChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'week';

    public function getHeading(): ?string
    {
        return 'Tasks Overview';
    }

    public function getMaxHeight(): ?string
    {
        return '300px';
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
            'quarter' => 'Last 3 months',
        ];
    }

    protected function getData(): array
    {
        $days = match ($this->filter) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            default => 7,
        };

        $labels = [];
        $completed = [];
        $created = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            if ($days <= 7) {
                $labels[] = $date->format('D');
            } elseif ($days <= 30) {
                $labels[] = $date->format('M d');
            } else {
                if ($i % 7 === 0) {
                    $labels[] = $date->format('M d');
                } else {
                    continue;
                }
            }

            $completed[] = Task::where('status', 'done')
                ->whereDate('updated_at', $date)
                ->count();

            $created[] = Task::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tasks Completed',
                    'data' => $completed,
                    'backgroundColor' => 'rgba(30, 58, 95, 0.8)',
                    'borderColor' => 'rgb(30, 58, 95)',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Tasks Created',
                    'data' => $created,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
