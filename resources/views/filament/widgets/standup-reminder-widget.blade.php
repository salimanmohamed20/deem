<x-filament-widgets::widget>
    <style>
        .standup-reminder-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .dark .standup-reminder-banner {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(245, 158, 11, 0.15));
        }

        .standup-reminder-banner.completed {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        }

        .dark .standup-reminder-banner.completed {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(22, 163, 74, 0.15));
        }

        .standup-reminder-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .standup-reminder-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .dark .standup-reminder-icon {
            background: rgba(0, 0, 0, 0.2);
        }

        .standup-reminder-icon svg {
            width: 20px;
            height: 20px;
            color: #f59e0b;
        }

        .standup-reminder-banner.completed .standup-reminder-icon svg {
            color: #22c55e;
        }

        .standup-reminder-text {
            font-size: 14px;
            font-weight: 600;
            color: #92400e;
        }

        .dark .standup-reminder-text {
            color: #fbbf24;
        }

        .standup-reminder-banner.completed .standup-reminder-text {
            color: #166534;
        }

        .dark .standup-reminder-banner.completed .standup-reminder-text {
            color: #4ade80;
        }

        .standup-reminder-btn {
            padding: 8px 16px;
            background: white;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #92400e;
            text-decoration: none;
            transition: all 0.15s;
            border: none;
            cursor: pointer;
        }

        .dark .standup-reminder-btn {
            background: rgba(0, 0, 0, 0.3);
            color: #fbbf24;
        }

        .standup-reminder-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 640px) {
            .standup-reminder-banner {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
            
            .standup-reminder-content {
                flex-direction: column;
            }
        }
    </style>

    <div class="standup-reminder-banner {{ $hasStandupToday ? 'completed' : '' }}">
        <div class="standup-reminder-content">
            <div class="standup-reminder-icon">
                @if($hasStandupToday)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </div>
            <span class="standup-reminder-text">
                @if($hasStandupToday)
                    ✓ Great job! You've submitted your daily standup for today
                @else
                    📝 Don't forget to submit your daily standup!
                @endif
            </span>
        </div>
        @if(!$hasStandupToday)
            <a href="{{ route('filament.admin.resources.standups.create') }}" class="standup-reminder-btn">
                Submit Standup Now
            </a>
        @endif
    </div>
</x-filament-widgets::widget>
