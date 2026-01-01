<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Standup;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StandupMetricsService
{
    public function getMonthlyStandups(Employee $employee, Carbon $month): Collection
    {
        return Standup::where('employee_id', $employee->id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->with('entries.project', 'entries.task')
            ->orderBy('date')
            ->get();
    }

    public function calculateMissedDays(Employee $employee, Carbon $month): int
    {
        $workingDays = $this->getWorkingDaysInMonth($month);
        $submittedDays = Standup::where('employee_id', $employee->id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->count();

        return max(0, $workingDays - $submittedDays);
    }

    public function calculateSubmissionRate(Employee $employee, Carbon $month): float
    {
        $workingDays = $this->getWorkingDaysInMonth($month);
        if ($workingDays === 0) {
            return 0;
        }

        $submittedDays = Standup::where('employee_id', $employee->id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->count();

        return round(($submittedDays / $workingDays) * 100, 2);
    }

    public function getTeamStandups(int $teamId, Carbon $date): Collection
    {
        return Standup::whereHas('employee.teams', function ($q) use ($teamId) {
            $q->where('teams.id', $teamId);
        })
            ->where('date', $date)
            ->with(['employee.user', 'entries.project', 'entries.task'])
            ->get();
    }

    public function calculateStreak(Employee $employee): int
    {
        // Only get last 60 days of standups for performance
        $standups = Standup::where('employee_id', $employee->id)
            ->where('date', '>=', Carbon::today()->subDays(60))
            ->orderBy('date', 'desc')
            ->pluck('date');

        if ($standups->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $currentDate = Carbon::today();

        foreach ($standups as $date) {
            $standupDate = Carbon::parse($date);

            // Skip weekends
            while ($currentDate->isWeekend()) {
                $currentDate->subDay();
            }

            if ($standupDate->isSameDay($currentDate)) {
                $streak++;
                $currentDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    private function getWorkingDaysInMonth(Carbon $month): int
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $workingDays = 0;

        while ($start <= $end) {
            if (!$start->isWeekend()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }
}
