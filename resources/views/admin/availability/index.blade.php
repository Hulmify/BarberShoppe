@extends('layouts.admin')

@section('title', 'Schedule')
@section('header', 'Weekly Availability')

@section('content')

<div class="card">
    <form action="{{ route('admin.availability.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="width: 150px;">Day</th>
                    <th>Working Hours</th>
                    <th style="width: 100px; text-align: center;">Active</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                @endphp
                
                @foreach($availabilities as $avail)
                    <tr>
                        <td style="font-weight: 600;">{{ $days[$avail->day_of_week] }}</td>
                        <td>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="time" name="schedule[{{ $avail->day_of_week }}][start_time]" 
                                    value="{{ \Carbon\Carbon::parse($avail->start_time)->format('H:i') }}" 
                                    style="width: 130px;"
                                    {{ $avail->is_active ? '' : 'disabled' }}
                                    id="start_{{ $avail->day_of_week }}"
                                >
                                <span style="color: #64748b;">to</span>
                                <input type="time" name="schedule[{{ $avail->day_of_week }}][end_time]" 
                                    value="{{ \Carbon\Carbon::parse($avail->end_time)->format('H:i') }}" 
                                    style="width: 130px;"
                                    {{ $avail->is_active ? '' : 'disabled' }}
                                    id="end_{{ $avail->day_of_week }}"
                                >
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <input type="checkbox" name="schedule[{{ $avail->day_of_week }}][is_active]" 
                                {{ $avail->is_active ? 'checked' : '' }}
                                style="width: 20px; height: 20px;"
                                onchange="toggleDay({{ $avail->day_of_week }}, this)"
                            >
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Save Schedule</button>
        </div>
    </form>
</div>

<script>
    function toggleDay(day, input) {
        const start = document.getElementById('start_' + day);
        const end = document.getElementById('end_' + day);
        
        if (input.checked) {
            start.disabled = false;
            end.disabled = false;
        } else {
            start.disabled = true;
            end.disabled = true;
        }
    }
</script>

@endsection
