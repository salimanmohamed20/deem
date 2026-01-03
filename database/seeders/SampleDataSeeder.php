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

        $devJobTitle = JobTitle::where('name', 'Software Developer')->first();
        $pmJobTitle = JobTitle::where('name', 'Project Manager')->first();
        $designerJobTitle = JobTitle::where('name', 'UI/UX Designer')->first();

        // ============================================
        // Create Project Manager User
        // ============================================
        $pmUser = User::firstOrCreate(
            ['email' => 'pm@example.com'],
            [
                'name' => 'Sarah Manager',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $pmUser->syncRoles(['project_manager']);

        $pmEmployee = Employee::firstOrCreate(
            ['user_id' => $pmUser->id],
            [
                'job_title_id' => $pmJobTitle->id,
                'hire_date' => now()->subMonths(12),
                'is_active' => true,
            ]
        );

        // ============================================
        // Create Employee Users
        // ============================================
        $employeeData = [
            ['name' => 'John Developer', 'email' => 'john@example.com', 'job_title_id' => $devJobTitle->id],
            ['name' => 'Jane Designer', 'email' => 'jane@example.com', 'job_title_id' => $designerJobTitle->id],
            ['name' => 'Bob Backend', 'email' => 'bob@example.com', 'job_title_id' => $devJobTitle->id],
            ['name' => 'Alice Frontend', 'email' => 'alice@example.com', 'job_title_id' => $devJobTitle->id],
        ];

        $employees = [$pmEmployee];
        foreach ($employeeData as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles(['employee']);

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

        // ============================================
        // Create Teams
        // ============================================
        $alphaTeam = Team::firstOrCreate(
            ['name' => 'Alpha Team'],
            [
                'description' => 'Main development team',
                'team_leader_id' => $pmEmployee->id,
            ]
        );
        // Add John, Jane, Bob to Alpha Team
        $alphaTeam->members()->sync([$employees[1]->id, $employees[2]->id, $employees[3]->id]);

        $betaTeam = Team::firstOrCreate(
            ['name' => 'Beta Team'],
            [
                'description' => 'Secondary development team',
                'team_leader_id' => $pmEmployee->id,
            ]
        );
        // Add Alice to Beta Team
        $betaTeam->members()->sync([$employees[4]->id]);

        // ============================================
        // Create Projects
        // ============================================
        
        // Project 1: Managed by PM, Alpha Team
        $project1 = Project::firstOrCreate(
            ['name' => 'Website Redesign'],
            [
                'description' => 'Complete redesign of company website',
                'project_manager_id' => $pmEmployee->id,
                'status' => 'active',
                'start_date' => now()->subWeeks(2),
                'end_date' => now()->addMonths(2),
            ]
        );
        $project1->teams()->sync([$alphaTeam->id]);

        // Project 2: Managed by PM, Beta Team
        $project2 = Project::firstOrCreate(
            ['name' => 'Mobile App'],
            [
                'description' => 'New mobile application development',
                'project_manager_id' => $pmEmployee->id,
                'status' => 'active',
                'start_date' => now()->subWeeks(1),
                'end_date' => now()->addMonths(3),
            ]
        );
        $project2->teams()->sync([$betaTeam->id]);

        // Project 3: Different manager (for testing isolation)
        $project3 = Project::firstOrCreate(
            ['name' => 'Internal Tools'],
            [
                'description' => 'Internal tools development',
                'project_manager_id' => null, // No manager assigned
                'status' => 'planned',
                'start_date' => now()->addWeeks(2),
                'end_date' => now()->addMonths(4),
            ]
        );

        // ============================================
        // Create Tasks for Project 1 (Website Redesign)
        // ============================================
        $project1Tasks = [
            ['title' => 'Design mockups', 'status' => 'done', 'priority' => 'high', 'assignee' => $employees[2]->id], // Jane
            ['title' => 'Implement homepage', 'status' => 'in_progress', 'priority' => 'high', 'assignee' => $employees[1]->id], // John
            ['title' => 'Create API endpoints', 'status' => 'in_progress', 'priority' => 'medium', 'assignee' => $employees[3]->id], // Bob
            ['title' => 'Write documentation', 'status' => 'to_do', 'priority' => 'low', 'assignee' => $employees[1]->id], // John
            ['title' => 'Setup CI/CD pipeline', 'status' => 'to_do', 'priority' => 'medium', 'assignee' => $employees[3]->id], // Bob
        ];

        foreach ($project1Tasks as $taskData) {
            $task = Task::firstOrCreate(
                ['title' => $taskData['title'], 'project_id' => $project1->id],
                [
                    'project_id' => $project1->id,
                    'description' => 'Task description for ' . $taskData['title'],
                    'status' => $taskData['status'],
                    'priority' => $taskData['priority'],
                    'deadline' => now()->addWeeks(rand(1, 4)),
                ]
            );
            $task->assignees()->sync([$taskData['assignee']]);
        }

        // ============================================
        // Create Tasks for Project 2 (Mobile App)
        // ============================================
        $project2Tasks = [
            ['title' => 'App wireframes', 'status' => 'done', 'priority' => 'high', 'assignee' => $employees[4]->id], // Alice
            ['title' => 'Setup React Native', 'status' => 'in_progress', 'priority' => 'high', 'assignee' => $employees[4]->id], // Alice
            ['title' => 'User authentication', 'status' => 'to_do', 'priority' => 'high', 'assignee' => $employees[4]->id], // Alice
        ];

        foreach ($project2Tasks as $taskData) {
            $task = Task::firstOrCreate(
                ['title' => $taskData['title'], 'project_id' => $project2->id],
                [
                    'project_id' => $project2->id,
                    'description' => 'Task description for ' . $taskData['title'],
                    'status' => $taskData['status'],
                    'priority' => $taskData['priority'],
                    'deadline' => now()->addWeeks(rand(1, 4)),
                ]
            );
            $task->assignees()->sync([$taskData['assignee']]);
        }

        // ============================================
        // Create Standups
        // ============================================
        foreach ($employees as $employee) {
            for ($i = 0; $i < 5; $i++) {
                $date = now()->subDays($i);
                if ($date->isWeekend()) continue;

                $standup = Standup::firstOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date->format('Y-m-d')],
                    ['employee_id' => $employee->id, 'date' => $date]
                );

                $projectForEntry = $employee->id === $employees[4]->id ? $project2 : $project1;

                StandupEntry::firstOrCreate(
                    ['standup_id' => $standup->id, 'project_id' => $projectForEntry->id],
                    [
                        'standup_id' => $standup->id,
                        'project_id' => $projectForEntry->id,
                        'what_i_did' => 'Worked on assigned tasks',
                        'what_i_will_do' => 'Continue with current work',
                        'blockers' => null,
                    ]
                );
            }
        }

        $this->command->info('Sample data created successfully!');
        $this->command->info('');
        $this->command->info('Test Users:');
        $this->command->info('  Super Admin: admin@admin.com / password');
        $this->command->info('  Project Manager: pm@example.com / password');
        $this->command->info('  Employee (John): john@example.com / password');
        $this->command->info('  Employee (Alice): alice@example.com / password');
    }
}
