@extends('layouts.admin')

@section('title', 'Schedule')
@section('header', 'Weekly Availability')
@section('subheader', 'Set your working hours for each day of the week.')

@section('content')

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <form action="{{ route('admin.availability.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-slate-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-bold w-1/4">Day</th>
                        <th scope="col" class="px-6 py-4 font-bold">Working Hours</th>
                        <th scope="col" class="px-6 py-4 font-bold text-center w-32">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    @endphp
                    
                    @foreach($availabilities as $avail)
                        <tr class="bg-white hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $days[$avail->day_of_week] }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4 transition-opacity duration-200" id="hours_{{ $avail->day_of_week }}" style="{{ $avail->is_active ? '' : 'opacity: 0.5; pointer-events: none;' }}">
                                    <div class="relative flex-1 max-w-[150px]">
                                        <select id="start_{{ $avail->day_of_week }}" name="schedule[{{ $avail->day_of_week }}][start_time]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
                                            @php
                                                $sTime = \Carbon\Carbon::createFromTime(0, 0);
                                                $currentStart = \Carbon\Carbon::parse($avail->start_time)->format('H:i');
                                            @endphp
                                            @for($i = 0; $i < 96; $i++)
                                                <option value="{{ $sTime->format('H:i') }}" {{ $currentStart == $sTime->format('H:i') ? 'selected' : '' }}>
                                                    {{ $sTime->format('h:i A') }}
                                                </option>
                                                @php $sTime->addMinutes(15); @endphp
                                            @endfor
                                        </select>
                                    </div>
                                    <span class="text-slate-400 font-medium">to</span>
                                    <div class="relative flex-1 max-w-[150px]">
                                        <select id="end_{{ $avail->day_of_week }}" name="schedule[{{ $avail->day_of_week }}][end_time]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
                                            @php
                                                $eTime = \Carbon\Carbon::createFromTime(0, 0);
                                                $currentEnd = \Carbon\Carbon::parse($avail->end_time)->format('H:i');
                                            @endphp
                                            @for($i = 0; $i < 96; $i++)
                                                <option value="{{ $eTime->format('H:i') }}" {{ $currentEnd == $eTime->format('H:i') ? 'selected' : '' }}>
                                                    {{ $eTime->format('h:i A') }}
                                                </option>
                                                @php $eTime->addMinutes(15); @endphp
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="schedule[{{ $avail->day_of_week }}][is_active]" 
                                           value="1" 
                                           class="sr-only peer" 
                                           {{ $avail->is_active ? 'checked' : '' }}
                                           onchange="toggleDay({{ $avail->day_of_week }}, this)">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-slate-900"></div>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-end">
            <button type="submit" class="text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:ring-slate-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none transition-transform hover:-translate-y-0.5">
                Save Schedule
            </button>
        </div>
    </form>
</div>

<script>
    function toggleDay(day, checkbox) {
        const wrapper = document.getElementById('hours_' + day);
        const start = document.getElementById('start_' + day);
        const end = document.getElementById('end_' + day);
        
        if (checkbox.checked) {
            wrapper.style.opacity = '1';
            wrapper.style.pointerEvents = 'auto';
            // Inputs are required if day is active
            start.setAttribute('required', 'required');
            end.setAttribute('required', 'required');
        } else {
            wrapper.style.opacity = '0.5';
            wrapper.style.pointerEvents = 'none';
            // Inputs not required if inactive, to prevent validation errors on hidden fields
            start.removeAttribute('required');
            end.removeAttribute('required');
        }
    }
</script>

@endsection
