@extends('layouts.admin')

@section('title', 'Appointments Calendar')
@section('header', 'Appointments Calendar')
@section('subheader', 'Visual overview of your shop\'s schedule.')

@section('content')

<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            List View
        </a>
        <div class="h-8 w-px bg-gray-200 mx-2"></div>
        <div class="flex items-center gap-4 text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:flex">
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500 shadow-sm shadow-yellow-500/50"></span> Pending</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-sm shadow-green-500/50"></span> Confirmed</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-sky-500 shadow-sm shadow-sky-500/50"></span> In Progress</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-slate-500 shadow-sm shadow-slate-500/50"></span> Completed</div>
            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-sm shadow-red-500/50"></span> Cancelled</div>
        </div>
    </div>
</div>

<div class="bg-white border border-gray-200 rounded-3xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
    <div id="calendar" class="min-h-[700px]"></div>
</div>

<!-- Modal for Appointment Details -->
<div id="eventModal" class="relative z-[60] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <!-- Modal Panel -->
    <div class="fixed inset-0 z-[70] w-screen overflow-y-auto pointer-events-none">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-auto">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                
                <!-- Modal Header -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-start mb-5">
                        <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight" id="modal-title">
                            Appointment Details
                        </h3>
                        <button type="button" onclick="closeModal()" class="rounded-full p-1 hover:bg-gray-100 text-slate-400 hover:text-slate-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Customer Info -->
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-12 h-12 rounded-full bg-white border border-gray-200 flex items-center justify-center text-primary-600 shadow-sm shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div class="overflow-hidden">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Customer</div>
                                <div class="text-lg font-bold text-slate-900 truncate" id="modal-customer"></div>
                                <div class="text-sm text-slate-500 truncate" id="modal-phone"></div>
                            </div>
                        </div>
                        
                        <!-- Status and Stylist Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 border border-gray-100 rounded-xl bg-white shadow-sm">
                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1.5">Status</div>
                                <div id="modal-status" class="inline-flex"></div>
                            </div>
                            <div class="p-3 border border-gray-100 rounded-xl bg-white shadow-sm">
                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-1.5">Stylist</div>
                                <div id="modal-stylist" class="text-sm font-bold text-slate-800"></div>
                            </div>
                        </div>

                        <!-- Time & Services -->
                        <div class="p-4 border border-gray-100 rounded-xl bg-white shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-gray-50 to-white -mr-8 -mt-8 rounded-full z-0"></div>
                            <div class="relative z-10">
                                <div class="text-[10px] font-bold text-slate-400 uppercase mb-2">Service Details</div>
                                <div id="modal-time" class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2"></div>
                                <div id="modal-services" class="text-sm text-slate-500 leading-relaxed bg-gray-50 p-2 rounded-lg mt-2 border border-gray-100"></div>
                            </div>
                        </div>
                        
                        <!-- Price -->
                        <div class="p-4 bg-slate-900 rounded-xl flex justify-between items-center shadow-lg">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total</span>
                            <span class="text-2xl font-black text-white" id="modal-price"></span>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3 border-t border-gray-100">
                     <form id="modal-delete-form" method="POST" onsubmit="return confirm('Permanently delete this appointment?');" class="m-0 sm:w-auto w-full">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-red-200 shadow-sm px-6 py-2.5 bg-white text-sm font-bold text-red-600 hover:bg-red-50 focus:outline-none transition-all hover:shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete
                        </button>
                    </form>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-2.5 bg-white text-sm font-bold text-slate-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto transition-all hover:shadow-md">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* FullCalendar Customization */
    :root {
        --fc-border-color: #e2e8f0;
        --fc-today-bg-color: #eff6ff;
        --fc-button-bg-color: #ffffff;
        --fc-button-border-color: #cbd5e1;
        --fc-button-hover-bg-color: #f8fafc;
        --fc-button-hover-border-color: #94a3b8;
        --fc-button-text-color: #475569;
        
        --fc-button-active-bg-color: #2563eb;
        --fc-button-active-border-color: #2563eb;
        --fc-button-active-text-color: #ffffff;
    }
    
    .fc { font-family: 'Outfit', sans-serif; }
    
    /* Header Toolbar */
    .fc .fc-toolbar.fc-header-toolbar {
        margin-bottom: 2rem;
        padding: 0 0.5rem;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.025em;
    }

    /* Buttons */
    .fc .fc-button-primary {
        background-color: var(--fc-button-bg-color) !important;
        border-color: var(--fc-button-border-color) !important;
        color: var(--fc-button-text-color) !important;
        font-weight: 600;
        text-transform: capitalize;
        font-size: 0.875rem;
        padding: 0.6rem 1.2rem;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        transition: all 0.2s;
    }
    
    .fc .fc-button-primary:hover {
        background-color: var(--fc-button-hover-bg-color) !important;
        border-color: var(--fc-button-hover-border-color) !important;
        color: #1e293b !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
        background-color: var(--fc-button-active-bg-color) !important;
        border-color: var(--fc-button-active-border-color) !important;
        color: var(--fc-button-active-text-color) !important;
        box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.05);
    }
    
    .fc .fc-button-group > .fc-button {
        border-radius: 0;
        margin-left: -1px;
    }
    .fc .fc-button-group > .fc-button:first-child {
        border-top-left-radius: 0.75rem;
        border-bottom-left-radius: 0.75rem;
        margin-left: 0;
    }
    .fc .fc-button-group > .fc-button:last-child {
        border-top-right-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
    }

    /* Grid & Headers */
    .fc .fc-col-header-cell {
        background-color: #f8fafc;
        padding: 12px 0;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .fc-col-header-cell-cushion {
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-decoration: none !important;
    }
    
    .fc-timegrid-axis-cushion {
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .fc-timegrid-slot-label-cushion {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
    }
    
    /* Events */
    .fc-event {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        margin: 1px 2px; /* Breathing room */
    }
    
    .fc-event-custom-content {
        height: 100%;
        width: 100%;
        border-radius: 6px;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1);
        padding: 4px 6px;
        border-left: 3px solid #6366f1; /* Default Color */
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        display: flex;
        flex-direction: column;
        justify-content: start;
        overflow: hidden;
    }
    
    .fc-event:hover .fc-event-custom-content {
        transform: translateY(-1px) scale(1.01);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        z-index: 50;
    }
    
    /* Time Grid Lines */
    .fc-timegrid-slot {
        height: 3rem; /* Make slots taller */
    }
    
    .fc-timegrid-now-indicator-line {
        border-color: #ef4444;
        border-width: 2px;
        box-shadow: 0 0 4px rgba(239, 68, 68, 0.4);
    }
    
    .fc-timegrid-now-indicator-arrow {
        border-color: #ef4444;
        border-width: 6px;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            navLinks: true, 
            businessHours: {
                daysOfWeek: [ 1, 2, 3, 4, 5, 6, 0 ], 
                startTime: '09:00', 
                endTime: '20:00', 
            },
            slotMinTime: '08:00:00',
            slotMaxTime: '22:00:00',
            allDaySlot: false,
            events: '{{ route("admin.appointments.events") }}',
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                showEventDetails(info.event);
            },
            nowIndicator: true,
            height: 'auto',
            contentHeight: 'auto',
            aspectRatio: 1.5,
            expandRows: true,
            stickyHeaderDates: true,
            slotDuration: '00:15:00', 
            slotLabelInterval: '01:00',
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },
            slotEventOverlap: false, // Cleaner look
            
            // Custom Event Rendering for Richer UI
            eventContent: function(arg) {
                // Parse properties
                const props = arg.event.extendedProps;
                const status = props.status || 'pending';
                const timeText = arg.timeText;
                
                // Extract customer and service from title as backup, or use props if available
                let titleParts = arg.event.title.split(' (');
                let customerName = props.customer || titleParts[0];
                let serviceName = titleParts[1] ? titleParts[1].replace(')', '') : 'Service';

                // Status Colors
                const statusColors = {
                    pending: '#eab308',   // yellow-500
                    confirmed: '#22c55e', // green-500
                    in_progress: '#0ea5e9', // sky-500
                    completed: '#64748b',   // slate-500
                    cancelled: '#ef4444'    // red-500
                };
                
                const borderColor = statusColors[status] || '#6366f1';
                const bgColor = statusColors[status] + '15'; // 15 = ~8% opacity hex
                
                return {
                    html: `
                        <div class="fc-event-custom-content" style="border-left-color: ${borderColor}; background-color: ${bgColor}">
                            <div class="flex items-center justify-between gap-1 w-full relative">
                                <div class="font-bold text-xs text-slate-800 truncate leading-tight">${customerName}</div>
                            </div>
                            <div class="text-[10px] text-slate-500 font-medium truncate mt-0.5">${serviceName}</div>
                            <div class="mt-auto flex items-center gap-1 text-[9px] font-bold text-slate-400 uppercase tracking-wide">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                ${timeText}
                            </div>
                        </div>
                    `
                };
            }
        });
        calendar.render();
    });

    function showEventDetails(event) {
        const props = event.extendedProps;
        document.getElementById('modal-customer').innerText = props.customer;
        document.getElementById('modal-phone').innerText = props.phone || 'No phone';
        document.getElementById('modal-stylist').innerText = props.stylist;
                
        const timeStr = event.start.toLocaleString('en-US', { 
            weekday: 'short', 
            month: 'short', 
            day: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit' 
        });
        document.getElementById('modal-time').innerHTML = `
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            ${timeStr}
        `;
        
        document.getElementById('modal-services').innerText = event.title.split(' (')[1] ? event.title.split(' (')[1].replace(')', '') : 'Service details not available';
        document.getElementById('modal-price').innerText = '{{ auth()->user()->shop->currency ?? "$" }} ' + props.price;
        
        const statusEl = document.getElementById('modal-status');
        const statusText = props.status.toUpperCase().replace('_', ' ');
        
        // Clean status styling
        let classes = 'inline-flex items-center rounded-full px-3 py-1 text-xs font-bold border shadow-sm ';
        if (props.status === 'pending') classes += 'bg-yellow-50 text-yellow-700 border-yellow-200';
        else if (props.status === 'confirmed') classes += 'bg-green-50 text-green-700 border-green-200';
        else if (props.status === 'in_progress') classes += 'bg-sky-50 text-sky-700 border-sky-200';
        else if (props.status === 'completed') classes += 'bg-slate-100 text-slate-700 border-slate-200';
        else if (props.status === 'cancelled') classes += 'bg-red-50 text-red-700 border-red-200';
        
        statusEl.className = classes;
        statusEl.innerText = statusText;
        
        // Update delete action
        const deleteForm = document.getElementById('modal-delete-form');
        deleteForm.action = '{{ url("admin/appointments") }}/' + event.id;

        const modal = document.getElementById('eventModal');
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('eventModal').classList.add('hidden');
    }
</script>
@endpush
