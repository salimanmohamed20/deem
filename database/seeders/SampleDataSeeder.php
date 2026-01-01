<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\JobTitle;
use App\Models\Project;
use App\Models\Standup;
use App\Models\StandupEntry;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create job titles
        $jobTitles = [
            ['name' => 'Software Developer', 'description' => 'Full-stack development'],
            ['name' => 'Project Manager', 'description' => 'Project management and coordination'],
            ['name' => 'UI/UX Designer', 'description' => 'User interface and experience design'],
            ['name' => 'QA Engineer', 'description' => 'Quality assurance and testing'],
            ['name' => 'DevOps Engineer', 'description' => 'Infrastructure and deployment'],
        ];

        foreach ($jobTitles as $jt) {
            JobTitle::firstOrCreate(['name' => $jt['name']], $jt);
        }

        // Create sample users and employees
        $devJobTitle = JobTitle::where('name', 'Software Developer')->first();
        $pmJobTitle = JobTitle::where('name', 'Project Manager')->first();

        $users = [
            ['name' => 'John Developer', 'email' => 'john@example.com', 'job_title_id' => $devJobTitle->id],
            ['name' => 'Jane Manager', 'email' => 'jane@example.com', 'job_title_id' => $pmJobTitle->id],
            ['name' => 'Bob Designer', 'email' => 'bob@example.com', 'job_title_id' => JobTitle::where('name', 'UI/UX Designer')->first()->id],
        ];

        $employees = [];
        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('employee');

            $employee = Employee::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'job_title_id' => $userData['job_title_id'],
                    'hire_date' => now()->subMonths(rand(1, 24)),
                    'is_active' => true,
                ]
            );
            $employees[] = $employee;
        }

        // Give Jane manager role
        User::where('email', 'jane@example.com')->first()->assignRole('manager');

        // Create teams
        $team = Team::firstOrCreate(
            ['name' => 'Alpha Team'],
            [
                'description' => 'Main development team',
                'team_leader_id' => $employees[1]->id, // Jane as leader
            ]
        );
        $team->members()->sync(collect($employees)->pluck('id'));

        // Create projects
        $project = Project::firstOrCreate(
            ['name' => 'Website Redesign'],
            [
                'description' => 'Complete redesign of company website',
                'project_manager_id' => $employees[1]->id,
                'status' => 'active',
                'start_date' => now()->subWeeks(2),
                'end_date' => now()->addMonths(2),
            ]
        );
        $project->teams()->sync([$team->id]);

        // Create tasks
        $tasks = [
            ['title' => 'Design mockups', 'status' => 'done', 'priority' => 'high'],
            ['title' => 'Implement homepage', 'status' => 'in_progress', 'priority' => 'high'],
            ['title' => 'Create API endpoints', 'status' => 'in_progress', 'priority' => 'medium'],
            ['title' => 'Write documentation', 'status' => 'to_do', 'priority' => 'low'],
            ['title' => 'Setup CI/CD pipeline', 'status' => 'to_do', 'priority' => 'medium'],
        ];

        foreach ($tasks as $taskData) {
            $task = Task::firstOrCreate(
                ['title' => $taskData['title'], 'project_id' => $project->id],
                [
                    'project_id' => $project->id,
                    'description' => 'Task description for ' . $taskData['title'],
                    'status' => $taskData['status'],
                    'priority' => $taskData['priority'],
                    'deadline' => now()->addWeeks(rand(1, 4)),
                ]
            );
            // Assign random employees
            $task->assignees()->sync([$employees[array_rand($employees)]->id]);
        }

        // Create sample standups
        foreach ($employees as $employee) {
            for ($i = 0; $i < 5; $i++) {
                $date = now()->subDays($i);
                if ($date->isWeekend())
                    continue;

                $standup = Standup::firstOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date->format('Y-m-d')],
                    ['employee_id' => $employee->id, 'date' => $date]
                );

                StandupEntry::firstOrCreate(
                    ['standup_id' => $standup->id, 'project_id' => $project->id],
                    [
                        'standup_id' => $standup->id,
                        'project_id' => $project->id,
                        'what_i_did' => 'Worked on assigned tasks',
                        'what_i_will_do' => 'Continue with current work',
                        'blockers' => null,
                    ]
                );
            }
        }
    }
}
