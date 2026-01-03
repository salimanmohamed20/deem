<x-filament-panels::page>
    {{-- FullCalendar CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    
    <style>
        .calendar-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .dark .calendar-container {
            background: #1f2937;
        }
        
        .fc {
            font-family: inherit;
        }
        
        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .dark .fc .fc-toolbar-title {
            color: #f3f4f6;
        }
        
        .fc .fc-button {
            background-color: #1e3a5f;
            border-color: #1e3a5f;
            font-weight: 500;
            text-transform: capitalize;
        }
        
        .fc .fc-button:hover {
            background-color: #172e4d;
            border-color: #172e4d;
        }
        
        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #0d1b2a;
            border-color: #0d1b2a;
        }
        
        .dark .fc .fc-col-header-cell-cushion,
        .dark .fc .fc-daygrid-day-number {
            color: #d1d5db;
        }
        
        .fc .fc-daygrid-day.fc-day-today {
            background-color: rgba(30, 58, 95, 0.1);
        }
        
        .dark .fc .fc-daygrid-day.fc-day-today {
            background-color: rgba(59, 130, 246, 0.15);
        }
        
        .fc-event {
            cursor: pointer;
            padding: 2px 4px;
            font-size: 0.75rem;
            border-radius: 4px;
        }
        
        .fc-event:hover {
            opacity: 0.9;
        }
        
        .fc-event-title {
            font-weight: 500;
        }
        
        /* Legend */
        .calendar-legend {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .dark .legend-item {
            color: #9ca3af;
        }
        
        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        /* Task Modal */
        .task-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
        
        .task-modal {
            background: white;
            border-radius: 12px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .dark .task-modal {
            background: #1f2937;
        }
        
        .task-modal-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: #1f2937;
        }
        
        .dark .task-modal-title {
            color: #f3f4f6;
        }
        
        .task-modal-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .dark .task-modal-row {
            border-color: #374151;
        }
        
        .task-modal-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .task-modal-value {
            color: #1f2937;
            font-weight: 500;
            font-size: 0.875rem;
        }
        
        .dark .task-modal-value {
            color: #f3f4f6;
        }
        
        .task-modal-actions {
            display: flex;
            gap: 8px;
            margin-top: 20px;
            justify-content: flex-end;
        }
        
        .task-modal-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
        }
        
        .task-modal-btn-primary {
            background: #1e3a5f;
            color: white;
            border: none;
        }
        
        .task-modal-btn-primary:hover {
            background: #172e4d;
        }
        
        .task-modal-btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: none;
        }
        
        .dark .task-modal-btn-secondary {
            background: #374151;
            color: #f3f4f6;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-to_do { background: #fef3c7; color: #92400e; }
        .status-in_progress { background: #dbeafe; color: #1e40af; }
        .status-done { background: #dcfce7; color: #166534; }
        
        .priority-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .priority-high { background: #fee2e2; color: #991b1b; }
        .priority-medium { background: #fef3c7; color: #92400e; }
        .priority-low { background: #dcfce7; color: #166534; }
    </style>

    <div x-data="taskCalendar()" x-init="initCalendar()">
        {{-- Filter --}}
        <div class="mb-4" style="max-width: 300px;">
            {{ $this->form }}
        </div>

        {{-- Legend --}}
        <div class="calendar-legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #f59e0b;"></span>
                <span>To Do</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #3b82f6;"></span>
                <span>In Progress</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #22c55e;"></span>
                <span>Done</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: white; border: 3px solid #ef4444;"></span>
                <span>High Priority</span>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="calendar-container">
            <div id="task-calendar"></div>
        </div>

        {{-- Task Detail Modal --}}
        <div x-show="showModal" 
             x-cloak
             class="task-modal-overlay"
             @click.self="showModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="task-modal" @click.stop>
                <h3 class="task-modal-title" x-text="selectedTask?.title"></h3>
                
                <div class="task-modal-row">
                    <span class="task-modal-label">Project</span>
                    <span class="task-modal-value" x-text="selectedTask?.extendedProps?.project || 'N/A'"></span>
                </div>
                
                <div class="task-modal-row">
                    <span class="task-modal-label">Status</span>
                    <span>
                        <span class="status-badge" 
                              :class="'status-' + selectedTask?.extendedProps?.status"
                              x-text="formatStatus(selectedTask?.extendedProps?.status)"></span>
                    </span>
                </div>
                
                <div class="task-modal-row">
                    <span class="task-modal-label">Priority</span>
                    <span>
                        <span class="priority-badge"
                              :class="'priority-' + selectedTask?.extendedProps?.priority"
                              x-text="capitalize(selectedTask?.extendedProps?.priority)"></span>
                    </span>
                </div>
                
                <div class="task-modal-row">
                    <span class="task-modal-label">Deadline</span>
                    <span class="task-modal-value" x-text="formatDate(selectedTask?.start)"></span>
                </div>
                
                <div class="task-modal-row" x-show="selectedTask?.extendedProps?.assignees">
                    <span class="task-modal-label">Assignees</span>
                    <span class="task-modal-value" x-text="selectedTask?.extendedProps?.assignees"></span>
                </div>
                
                <div class="task-modal-actions">
                    <button class="task-modal-btn task-modal-btn-secondary" @click="showModal = false">
                        Close
                    </button>
                    <a :href="'/admin/tasks/' + selectedTask?.extendedProps?.taskId + '/edit'" 
                       class="task-modal-btn task-modal-btn-primary">
                        View Task
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- FullCalendar JS --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    
    <script>
        function taskCalendar() {
            return {
                calendar: null,
                showModal: false,
                selectedTask: null,
                events: @json($this->calendarEvents),
                canDrag: @json(\App\Traits\HasRoleBasedAccess::isSuperAdmin() || \App\Traits\HasRoleBasedAccess::isProjectManager()),
                
                initCalendar() {
                    const calendarEl = document.getElementById('task-calendar');
                    
                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,listWeek'
                        },
                        events: this.events,
                        editable: this.canDrag,
                        eventClick: (info) => {
                            this.selectedTask = info.event;
                            this.showModal = true;
                        },
                        eventDrop: (info) => {
                            if (this.canDrag) {
                                const newDate = info.event.startStr;
                                const taskId = info.event.extendedProps.taskId;
                                this.$wire.updateTaskDate(taskId, newDate);
                            }
                        },
                        height: 'auto',
                        dayMaxEvents: 3,
                        moreLinkClick: 'popover',
                    });
                    
                    this.calendar.render();
                    
                    // Listen for updates from Livewire
                    Livewire.on('calendar-events-updated', (data) => {
                        this.calendar.removeAllEvents();
                        this.calendar.addEventSource(data.events);
                    });
                },
                
                formatStatus(status) {
                    const statusMap = {
                        'to_do': 'To Do',
                        'in_progress': 'In Progress',
                        'done': 'Done'
                    };
                    return statusMap[status] || status;
                },
                
                capitalize(str) {
                    if (!str) return '';
                    return str.charAt(0).toUpperCase() + str.slice(1);
                },
                
                formatDate(dateStr) {
                    if (!dateStr) return 'N/A';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('en-US', { 
                        weekday: 'short',
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                }
            }
        }
    </script>
</x-filament-panels::page>
