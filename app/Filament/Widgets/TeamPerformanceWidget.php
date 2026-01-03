<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Traits\HasRoleBasedAccess;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TeamPerformanceWidget extends ChartWidget
{
    use HasRoleBasedAccess;

    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return 'Task Priority Breakdown';
    }

    public function getDescription(): ?string
    {
        return 'Distribution by priority level';
    }

    public function getMaxHeight(): ?string
    {
        return '280px';
    }

    public static function canView(): bool
    {
        return self::isSuperAdmin() || self::isProjectManager();
    }

    protected function getData(): array
    {
        $query = Task::query()->forCurrentEmployee();

        $high = (clone $query)->where('priority', 'high')->count();
        $medium = (clone $query)->where('priority', 'medium')->count();
        $low = (clone $query)->where('priority', 'low')->count();

        return [
            'datasets' => [
                [
                    'data' => [$high, $medium, $low],
                    'backgroundColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(34, 197, 94)',
                    ],
                    'borderWidth' => 0,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => ['High', 'Medium', 'Low'],
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
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
            'scales' => [
                'r' => [
                    'display' => false,
                ],
            ],
            'maintainAspectRatio' => true,
        ];
    }
}
