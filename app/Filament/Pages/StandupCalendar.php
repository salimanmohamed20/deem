<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use App\Models\Standup;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;
use Illuminate\Support\Facades\DB;

class StandupCalendar extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected string $view = 'filament.pages.standup-calendar';

    public ?int $selectedEmployeeId = null;
    public array $metrics = [];
    public array $calendarEvents = [];

    public function mount(): void
    {
        $this->selectedEmployeeId = auth()->user()->employee?->id;
        $this->loadStandups();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('selectedEmployeeId')
                    ->label('Employee')
                    ->options(fn () => Employee::join('users', 'employees.user_id', '=', 'users.id')
                        ->pluck('users.name', 'employees.id'))
                    ->searchable()
                    ->live()
                    ->placeholder('Select an employee'),
            ])
            ->columns(1);
    }

    public function updatedSelectedEmployeeId(): void
    {
        $this->loadStandups();
        $this->dispatch('standups-updated', $this->calendarEvents);
    }

    public function loadStandups(): void
    {
        if (!$this->selectedEmployeeId) {
            $this->metrics = [];
            $this->calendarEvents = [];
            return;
        }

        $currentMonth = Carbon::now();
        
        // Get only last 3 months of standups for calendar
        $standups = DB::table('standups')
            ->where('employee_id', $this->selectedEmployeeId)
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
            ->where('employee_id', $this->selectedEmployeeId)
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->count();

        $this->metrics = [
            'total' => $standups->count(),
            'thisMonth' => $thisMonthCount,
            'rate' => round(($thisMonthCount / 22) * 100, 0),
        ];
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
