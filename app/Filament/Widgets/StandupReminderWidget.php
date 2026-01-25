<?php

namespace App\Filament\Widgets;

use App\Models\Standup;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class StandupReminderWidget extends Widget
{
    protected static ?int $sort = 0; // Display at the top
    
    protected int | string | array $columnSpan = 'full'; // Full width

    public bool $hasStandupToday = false;
    
    protected string $view = 'filament.widgets.standup-reminder-widget';

    public function mount(): void
    {
        $this->checkStandupStatus();
    }

    protected function checkStandupStatus(): void
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            $this->hasStandupToday = true; // Hide banner if no employee record
            return;
        }

        $today = Carbon::today();
        $todayStandup = Standup::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        $this->hasStandupToday = $todayStandup !== null;
    }

    public static function canView(): bool
    {
        // Show to all users who have an employee record
        return auth()->user()?->employee !== null;
    }
}
