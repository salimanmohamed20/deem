<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StandupMetricsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return [
                Stat::make('Standups', 'N/A')
                    ->description('No employee profile linked')
                    ->descriptionIcon('heroicon-m-exclamation-circle')
                    ->color('gray'),
            ];
        }

        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        // This month's standups
        $thisMonthCount = DB::table('standups')
            ->where('employee_id', $employee->id)
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->count();

        // Last month's standups for comparison
        $lastMonthCount = DB::table('standups')
            ->where('employee_id', $employee->id)
            ->whereYear('date', $lastMonth->year)
            ->whereMonth('date', $lastMonth->month)
            ->count();

        // Calculate working days passed this month
        $workingDaysPassed = $this->getWorkingDaysPassed($currentMonth);
        $totalWorkingDays = $this->getTotalWorkingDays($currentMonth);
        
        $rate = $workingDaysPassed > 0 ? round(($thisMonthCount / $workingDaysPassed) * 100, 0) : 0;
        $missed = max(0, $workingDaysPassed - $thisMonthCount);

        // Streak calculation
        $streak = $this->getCurrentStreak($employee->id);

        // Trend comparison
        $trend = $thisMonthCount - $lastMonthCount;
        $trendIcon = $trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

        return [
            Stat::make('Submission Rate', $rate . '%')
                ->description("{$thisMonthCount} of {$workingDaysPassed} days")
                ->descriptionIcon($rate >= 80 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->chart($this->getWeeklySubmissions($employee->id))
                ->color($rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger')),
            Stat::make('Current Streak', $streak . ' days')
                ->description($streak >= 5 ? 'Great consistency!' : 'Keep it up!')
                ->descriptionIcon('heroicon-m-fire')
                ->color($streak >= 5 ? 'success' : 'info'),
            Stat::make('Missed Days', $missed)
                ->description('This month')
                ->descriptionIcon($missed == 0 ? 'heroicon-m-check-circle' : 'heroicon-m-calendar')
                ->color($missed == 0 ? 'success' : ($missed <= 3 ? 'warning' : 'danger')),
        ];
    }

    private function getWorkingDaysPassed(Carbon $month): int
    {
        $start = $month->copy()->startOfMonth();
        $end = min($month->copy()->endOfMonth(), Carbon::now());
        $count = 0;

        while ($start <= $end) {
            if ($start->isWeekday()) {
                $count++;
            }
            $start->addDay();
        }

        return $count;
    }

    private function getTotalWorkingDays(Carbon $month): int
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $count = 0;

        while ($start <= $end) {
            if ($start->isWeekday()) {
                $count++;
            }
            $start->addDay();
        }

        return $count;
    }

    private function getCurrentStreak(int $employeeId): int
    {
        $streak = 0;
        $date = Carbon::today();

        while (true) {
            // Skip weekends
            if ($date->isWeekend()) {
                $date->subDay();
                continue;
            }

            $hasStandup = DB::table('standups')
                ->where('employee_id', $employeeId)
                ->whereDate('date', $date)
                ->exists();

            if ($hasStandup) {
                $streak++;
                $date->subDay();
            } else {
                break;
            }

            // Safety limit
            if ($streak > 365) break;
        }

        return $streak;
    }

    private function getWeeklySubmissions(int $employeeId): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $hasStandup = DB::table('standups')
                ->where('employee_id', $employeeId)
                ->whereDate('date', $date)
                ->exists();
            $data[] = $hasStandup ? 1 : 0;
        }
        return $data;
    }
}
