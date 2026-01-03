<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Traits\HasRoleBasedAccess;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    use HasRoleBasedAccess;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();

        if (self::isSuperAdmin($user)) {
            return $this->getSuperAdminStats();
        }

        if (self::isProjectManager($user)) {
            return $this->getProjectManagerStats();
        }

        return $this->getEmployeeStats();
    }

    private function getSuperAdminStats(): array
    {
        $totalUsers = User::count();
        $newUsersThisWeek = User::where('created_at', '>=', now()->subWeek())->count();
        
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        
        $totalTasks = Task::count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();
        $completedThisWeek = Task::where('status', 'done')
            ->where('updated_at', '>=', now()->subWeek())->count();
        
        $activeEmployees = Employee::where('is_active', true)->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description($newUsersThisWeek > 0 ? "+{$newUsersThisWeek} this week" : 'All system users')
                ->descriptionIcon($newUsersThisWeek > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-users')
                ->chart($this->getWeeklyTrend(User::class))
                ->color('primary'),
            Stat::make('Projects', $totalProjects)
                ->description("{$activeProjects} active")
                ->descriptionIcon('heroicon-m-folder-open')
                ->chart($this->getWeeklyTrend(Project::class))
                ->color('success'),
            Stat::make('Tasks', $totalTasks)
                ->description("{$inProgressTasks} in progress")
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart($this->getWeeklyTrend(Task::class))
                ->color('warning'),
            Stat::make('Employees', $activeEmployees)
                ->description("{$completedThisWeek} tasks done this week")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }

    private function getProjectManagerStats(): array
    {
        $projectIds = self::getAccessibleProjectIds();
        
        $totalTasks = Task::whereIn('project_id', $projectIds)->count();
        $completedTasks = Task::whereIn('project_id', $projectIds)->where('status', 'done')->count();
        $inProgressTasks = Task::whereIn('project_id', $projectIds)->where('status', 'in_progress')->count();
        $todoTasks = Task::whereIn('project_id', $projectIds)->where('status', 'to_do')->count();
        
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        $teamMembers = Employee::whereHas('tasks', function ($q) use ($projectIds) {
            $q->whereIn('project_id', $projectIds);
        })->count();

        $overdueTasks = Task::whereIn('project_id', $projectIds)
            ->where('status', '!=', 'done')
            ->where('deadline', '<', now())
            ->count();

        return [
            Stat::make('My Projects', count($projectIds))
                ->description('Projects you manage')
                ->descriptionIcon('heroicon-m-folder-open')
                ->color('primary'),
            Stat::make('Completion Rate', "{$completionRate}%")
                ->description("{$completedTasks} of {$totalTasks} tasks")
                ->descriptionIcon($completionRate >= 70 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([$todoTasks, $inProgressTasks, $completedTasks])
                ->color($completionRate >= 70 ? 'success' : 'warning'),
            Stat::make('In Progress', $inProgressTasks)
                ->description($overdueTasks > 0 ? "{$overdueTasks} overdue" : 'On track')
                ->descriptionIcon($overdueTasks > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($overdueTasks > 0 ? 'danger' : 'info'),
            Stat::make('Team Members', $teamMembers)
                ->description('Working on your projects')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('gray'),
        ];
    }

    private function getEmployeeStats(): array
    {
        $user = auth()->user();
        $employeeId = $user->employee?->id;

        if (!$employeeId) {
            return [];
        }

        $myTasks = Task::whereHas('assignees', function ($q) use ($employeeId) {
            $q->where('employees.id', $employeeId);
        });

        $totalTasks = (clone $myTasks)->count();
        $todoTasks = (clone $myTasks)->where('status', 'to_do')->count();
        $inProgressTasks = (clone $myTasks)->where('status', 'in_progress')->count();
        $completedTasks = (clone $myTasks)->where('status', 'done')->count();
        
        $completedThisWeek = (clone $myTasks)
            ->where('status', 'done')
            ->where('updated_at', '>=', now()->subWeek())
            ->count();

        $overdueTasks = (clone $myTasks)
            ->where('status', '!=', 'done')
            ->where('deadline', '<', now())
            ->count();

        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return [
            Stat::make('My Tasks', $totalTasks)
                ->description("{$completedThisWeek} completed this week")
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([$todoTasks, $inProgressTasks, $completedTasks])
                ->color('primary'),
            Stat::make('To Do', $todoTasks)
                ->description($overdueTasks > 0 ? "{$overdueTasks} overdue!" : 'Pending tasks')
                ->descriptionIcon($overdueTasks > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-clock')
                ->color($overdueTasks > 0 ? 'danger' : 'warning'),
            Stat::make('In Progress', $inProgressTasks)
                ->description('Currently working on')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),
            Stat::make('Completed', "{$completionRate}%")
                ->description("{$completedTasks} tasks done")
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart($this->getMyWeeklyCompletions($employeeId))
                ->color('success'),
        ];
    }

    private function getWeeklyTrend(string $model): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = $model::whereDate('created_at', now()->subDays($i))->count();
        }
        return $data;
    }

    private function getMyWeeklyCompletions(int $employeeId): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = Task::whereHas('assignees', function ($q) use ($employeeId) {
                $q->where('employees.id', $employeeId);
            })
            ->where('status', 'done')
            ->whereDate('updated_at', now()->subDays($i))
            ->count();
        }
        return $data;
    }
}
