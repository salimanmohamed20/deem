<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StandupMetricsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return [
                Stat::make('Standups', 'N/A')
                    ->description('No employee profile')
                    ->color('gray'),
            ];
        }

        $currentMonth = Carbon::now();
        
        // Single query for this month's standups
        $submittedDays = DB::table('standups')
            ->where('employee_id', $employee->id)
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->count();

        // Calculate working days (simple approximation)
        $workingDays = 22; // Average working days per month
        $rate = $workingDays > 0 ? round(($submittedDays / $workingDays) * 100, 0) : 0;
        $missed = max(0, $workingDays - $submittedDays);

        return [
            Stat::make('Submission Rate', $rate . '%')
                ->description('This month')
                ->color($rate >= 80 ? 'success' : 'warning'),
            Stat::make('Submitted', $submittedDays . ' days')
                ->description('This month')
                ->color('info'),
            Stat::make('Missed Days', $missed)
                ->description('This month')
                ->color($missed == 0 ? 'success' : 'danger'),
        ];
    }
}
