<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Traits\HasRoleBasedAccess;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TasksChartWidget extends ChartWidget
{
    use HasRoleBasedAccess;

    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    public ?string $filter = 'week';

    public function getHeading(): ?string
    {
        return self::isEmployee() ? 'My Activity' : 'Task Activity';
    }

    public function getDescription(): ?string
    {
        return 'Tasks created vs completed over time';
    }

    public function getMaxHeight(): ?string
    {
        return '320px';
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

        $step = $days <= 7 ? 1 : ($days <= 30 ? 1 : 7);

        for ($i = $days - 1; $i >= 0; $i -= $step) {
            $date = Carbon::today()->subDays($i);
            
            if ($days <= 7) {
                $labels[] = $date->format('D');
            } elseif ($days <= 30) {
                $labels[] = $date->format('M d');
            } else {
                $labels[] = $date->format('M d');
            }

            $baseQuery = Task::query()->forCurrentEmployee();

            if ($step > 1) {
                $startDate = $date;
                $endDate = Carbon::today()->subDays(max(0, $i - $step + 1));
                
                $completed[] = (clone $baseQuery)
                    ->where('status', 'done')
                    ->whereBetween('updated_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                    ->count();

                $created[] = (clone $baseQuery)
                    ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                    ->count();
            } else {
                $completed[] = (clone $baseQuery)
                    ->where('status', 'done')
                    ->whereDate('updated_at', $date)
                    ->count();

                $created[] = (clone $baseQuery)
                    ->whereDate('created_at', $date)
                    ->count();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Completed',
                    'data' => $completed,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(34, 197, 94)',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
                [
                    'label' => 'Created',
                    'data' => $created,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(99, 102, 241)',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'align' => 'end',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'padding' => 20,
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.05)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'maintainAspectRatio' => true,
        ];
    }
}
