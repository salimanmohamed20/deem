<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Collection;

class MentionService
{
    public function extractMentions(string $text): array
    {
        preg_match_all('/@(\w+)/', $text, $matches);
        return $matches[1] ?? [];
    }

    public function validateMentions(array $mentions, array $allowedEmployeeIds): array
    {
        $employees = Employee::with('user')
            ->whereIn('id', $allowedEmployeeIds)
            ->get();

        $validMentions = [];
        foreach ($mentions as $mention) {
            $employee = $employees->first(function ($emp) use ($mention) {
                return strtolower($emp->user->name) === strtolower($mention) ||
                    str_contains(strtolower($emp->user->name), strtolower($mention));
            });

            if ($employee) {
                $validMentions[] = $employee->id;
            }
        }

        return array_unique($validMentions);
    }

    public function formatMentionsForDisplay(string $text, Collection $employees): string
    {
        foreach ($employees as $employee) {
            $pattern = '/@' . preg_quote($employee->user->name, '/') . '/i';
            $replacement = '<a href="#" class="mention text-primary-600 font-semibold">@' . $employee->user->name . '</a>';
            $text = preg_replace($pattern, $replacement, $text);
        }

        return $text;
    }
}
