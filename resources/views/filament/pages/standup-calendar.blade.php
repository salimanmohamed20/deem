<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Employee Selector --}}
        <div class="max-w-xs">
            {{ $this->form }}
        </div>

        {{-- Metrics Cards --}}
        @if(!empty($this->metrics))
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-filament::section>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600">{{ $this->metrics['total'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Total Standups</div>
                    </div>
                </x-filament::section>
                <x-filament::section>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-success-600">{{ $this->metrics['thisMonth'] ?? 0 }}</div>
                        <div class="text-sm text-gray-500">This Month</div>
                    </div>
                </x-filament::section>
                <x-filament::section>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-info-600">{{ $this->metrics['rate'] ?? 0 }}%</div>
                        <div class="text-sm text-gray-500">Rate</div>
                    </div>
                </x-filament::section>
            </div>
        @endif

        {{-- Calendar --}}
        <x-filament::section>
            <div id="standup-calendar" wire:ignore></div>
        </x-filament::section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    
    <script>
        let calendar = null;
        
        function initCalendar() {
            const el = document.getElementById('standup-calendar');
            if (!el || calendar) return;
            
            calendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'today'
                },
                events: @json($this->calendarEvents),
                height: 500,
                eventDisplay: 'block',
            });
            
            calendar.render();
        }
        
        document.addEventListener('DOMContentLoaded', initCalendar);
        document.addEventListener('livewire:navigated', initCalendar);
        
        Livewire.on('standups-updated', (events) => {
            if (calendar) {
                calendar.removeAllEvents();
                if (events && events[0]) {
                    calendar.addEventSource(events[0]);
                }
            }
        });
    </script>
</x-filament-panels::page>
