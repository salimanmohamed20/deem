<x-filament-panels::page>
    <div class="space-y-6">
        {{-- User Info Header --}}
        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            {{ strtoupper(substr($this->record->name, 0, 1)) }}
                        </span>
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $this->record->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->record->email }}</p>
                    <div class="flex gap-2 mt-2">
                        @foreach($this->record->roles as $role)
                            <x-filament::badge>{{ $role->name }}</x-filament::badge>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Standup Calendar Section --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <x-heroicon-o-calendar class="w-5 h-5" />
                Standup Calendar
            </h3>

            @if($this->record->employee)
                {{-- Metrics Cards --}}
              
                {{-- Calendar --}}
                <x-filament::section>
                    <div id="user-standup-calendar" wire:ignore></div>
                </x-filament::section>
            @else
                <x-filament::section>
                    <div class="text-center py-8">
                        <x-heroicon-o-user-circle class="w-12 h-12 mx-auto text-gray-400" />
                        <p class="mt-2 text-gray-500 dark:text-gray-400">This user is not linked to an employee profile.</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Standup data is not available.</p>
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>

    @if($this->record->employee)
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        
        <style>
            /* FullCalendar Filament v4 Theme */
            .fc {
                font-family: inherit;
            }
            
            /* Header toolbar */
            .fc .fc-toolbar {
                flex-wrap: wrap;
                gap: 0.75rem;
                margin-bottom: 1.25rem !important;
            }
            
            .fc .fc-toolbar-title {
                font-size: 1.125rem !important;
                font-weight: 600 !important;
                color: rgb(17 24 39) !important;
            }
            
            .dark .fc .fc-toolbar-title {
                color: rgb(243 244 246) !important;
            }
            
            /* Buttons */
            .fc .fc-button {
                background-color: rgb(59 130 246) !important;
                border-color: rgb(59 130 246) !important;
                border-radius: 0.5rem !important;
                font-weight: 500 !important;
                font-size: 0.875rem !important;
                padding: 0.5rem 1rem !important;
                text-transform: none !important;
                box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
                transition: all 150ms !important;
            }
            
            .fc .fc-button:hover {
                background-color: rgb(37 99 235) !important;
                border-color: rgb(37 99 235) !important;
            }
            
            .fc .fc-button:focus {
                box-shadow: 0 0 0 2px rgb(59 130 246 / 0.5) !important;
            }
            
            .fc .fc-button-primary:disabled {
                background-color: rgb(156 163 175) !important;
                border-color: rgb(156 163 175) !important;
            }
            
            .fc .fc-button-primary:not(:disabled).fc-button-active,
            .fc .fc-button-primary:not(:disabled):active {
                background-color: rgb(29 78 216) !important;
                border-color: rgb(29 78 216) !important;
            }
            
            .fc .fc-button-group > .fc-button {
                border-radius: 0 !important;
            }
            
            .fc .fc-button-group > .fc-button:first-child {
                border-top-left-radius: 0.5rem !important;
                border-bottom-left-radius: 0.5rem !important;
            }
            
            .fc .fc-button-group > .fc-button:last-child {
                border-top-right-radius: 0.5rem !important;
                border-bottom-right-radius: 0.5rem !important;
            }
            
            /* Table borders */
            .fc .fc-scrollgrid {
                border-color: rgb(229 231 235) !important;
                border-radius: 0.5rem !important;
                overflow: hidden;
            }
            
            .dark .fc .fc-scrollgrid {
                border-color: rgb(55 65 81) !important;
            }
            
            .fc th, .fc td {
                border-color: rgb(229 231 235) !important;
            }
            
            .dark .fc th, .dark .fc td {
                border-color: rgb(55 65 81) !important;
            }
            
            /* Header cells */
            .fc .fc-col-header-cell {
                background-color: rgb(249 250 251) !important;
                padding: 0.75rem 0 !important;
            }
            
            .dark .fc .fc-col-header-cell {
                background-color: rgb(31 41 55) !important;
            }
            
            .fc .fc-col-header-cell-cushion {
                color: rgb(107 114 128) !important;
                font-weight: 600 !important;
                font-size: 0.75rem !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
                text-decoration: none !important;
            }
            
            .dark .fc .fc-col-header-cell-cushion {
                color: rgb(156 163 175) !important;
            }
            
            /* Day cells */
            .fc .fc-daygrid-day {
                background-color: white !important;
            }
            
            .dark .fc .fc-daygrid-day {
                background-color: rgb(17 24 39) !important;
            }
            
            .fc .fc-daygrid-day-number {
                color: rgb(55 65 81) !important;
                font-weight: 500 !important;
                padding: 0.5rem !important;
                text-decoration: none !important;
            }
            
            .dark .fc .fc-daygrid-day-number {
                color: rgb(209 213 219) !important;
            }
            
            /* Today */
            .fc .fc-day-today {
                background-color: rgb(239 246 255) !important;
            }
            
            .dark .fc .fc-day-today {
                background-color: rgb(30 58 138 / 0.2) !important;
            }
            
            .fc .fc-day-today .fc-daygrid-day-number {
                background-color: rgb(59 130 246) !important;
                color: white !important;
                border-radius: 9999px !important;
                width: 1.75rem !important;
                height: 1.75rem !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                margin: 0.25rem !important;
                padding: 0 !important;
            }
            
            /* Events */
            .fc .fc-event {
                border-radius: 0.375rem !important;
                border: none !important;
                padding: 0.125rem 0.5rem !important;
                font-size: 0.75rem !important;
                font-weight: 500 !important;
                cursor: default !important;
            }
            
            .fc .fc-daygrid-event-dot {
                display: none !important;
            }
            
            /* Other month days */
            .fc .fc-day-other .fc-daygrid-day-number {
                color: rgb(156 163 175) !important;
            }
            
            .dark .fc .fc-day-other .fc-daygrid-day-number {
                color: rgb(75 85 99) !important;
            }
            
            .fc .fc-day-other {
                background-color: rgb(249 250 251) !important;
            }
            
            .dark .fc .fc-day-other {
                background-color: rgb(17 24 39 / 0.5) !important;
            }
            
            /* More link */
            .fc .fc-daygrid-more-link {
                color: rgb(59 130 246) !important;
                font-weight: 500 !important;
                font-size: 0.75rem !important;
            }
        </style>
        
        <script>
            let userCalendar = null;
            
            function initUserCalendar() {
                const el = document.getElementById('user-standup-calendar');
                if (!el) return;
                
                if (userCalendar) {
                    userCalendar.destroy();
                    userCalendar = null;
                }
                
                userCalendar = new FullCalendar.Calendar(el, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,dayGridWeek'
                    },
                    buttonText: {
                        today: 'Today',
                        month: 'Month',
                        week: 'Week'
                    },
                    events: @json($this->calendarEvents),
                    height: 'auto',
                    aspectRatio: 1.6,
                    eventDisplay: 'block',
                    dayMaxEvents: 2,
                    fixedWeekCount: false,
                    showNonCurrentDates: true,
                });
                
                userCalendar.render();
            }
            
            document.addEventListener('DOMContentLoaded', initUserCalendar);
            document.addEventListener('livewire:navigated', () => {
                setTimeout(initUserCalendar, 100);
            });
        </script>
    @endif
</x-filament-panels::page>
