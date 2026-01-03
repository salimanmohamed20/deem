<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Standup;
use Carbon\Carbon;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;


    public array $metrics = [];
    public array $calendarEvents = [];

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->loadStandups();
    }

    public function loadStandups(): void
    {
        $employee = $this->record->employee;
        
        if (!$employee) {
            $this->metrics = [];
            $this->calendarEvents = [];
            return;
        }

        $currentMonth = Carbon::now();
        
        // Get only last 3 months of standups for calendar
        $standups = DB::table('standups')
            ->where('employee_id', $employee->id)
            ->where('date', '>=', $currentMonth->copy()->subMonths(3)->startOfMonth())
            ->select('id', 'date')
            ->orderBy('date', 'desc')
            ->get();

        // Build calendar events
        $this->calendarEvents = $standups->map(function ($standup) {
            return [
                'id' => $standup->id,
                'title' => 'Submitted',
                'start' => $standup->date,
                'backgroundColor' => '#22c55e',
                'borderColor' => '#16a34a',
            ];
        })->toArray();

        // Simple metrics
        $thisMonthCount = DB::table('standups')
            ->where('employee_id', $employee->id)
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->count();

        $workingDays = $this->getWorkingDaysInMonth($currentMonth);

        $this->metrics = [
            'total' => $standups->count(),
            'thisMonth' => $thisMonthCount,
            'rate' => $workingDays > 0 ? round(($thisMonthCount / $workingDays) * 100, 0) : 0,
        ];
    }

    private function getWorkingDaysInMonth(Carbon $date): int
    {
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();
        $workingDays = 0;

        while ($start <= $end) {
            if (!$start->isWeekend()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    public function getCalendarEventsProperty(): array
    {
        return $this->calendarEvents;
    }

    public function getMetricsProperty(): array
    {
        return $this->metrics;
    }
}
