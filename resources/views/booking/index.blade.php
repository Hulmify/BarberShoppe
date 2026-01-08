<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $shop->name }} | Book Appointment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-slate-900 min-h-screen flex flex-col items-center py-10 px-4">

    <div class="w-full max-w-3xl">
        <header class="text-center mb-10 animate-fade-in-down">
            <h1 class="text-4xl font-extrabold tracking-tight mb-2 text-transparent bg-clip-text bg-gradient-to-r from-slate-800 to-amber-600">
                {{ $shop->name }}
            </h1>
            <p class="text-lg text-slate-500">{{ $shop->description ?? 'Premium Barber Services' }}</p>
        </header>

        <form id="bookingForm" onsubmit="submitBooking(event)" class="space-y-6">
            
            <!-- Step 1: Services -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8 transition-transform hover:scale-[1.01] duration-300">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 text-sm font-bold">1</span>
                    Select Services
                </h2>
                <div class="grid gap-4">
                    @foreach($services as $service)
                        <div class="service-item group relative flex items-center justify-between p-4 rounded-xl border-2 border-gray-100 hover:border-amber-400 cursor-pointer transition-all duration-200"
                             onclick="toggleService(this, {{ $service->id }}, {{ $service->price }}, {{ $service->duration_minutes }})">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-slate-900">{{ $service->name }}</h3>
                                <div class="text-sm text-slate-500 flex items-center gap-2 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    {{ $service->duration_minutes }} min
                                    <span class="mx-1">•</span>
                                    <span>{{ $service->description }}</span>
                                </div>
                            </div>
                            <div class="text-xl font-bold text-slate-900 group-hover:text-amber-600 transition-colors">
                                {{ $shop->currency ?? '$' }} {{ $service->price }}
                            </div>
                            <!-- Selected Checkmark Indicator (Hidden by default) -->
                            <div class="absolute top-0 right-0 -mt-2 -mr-2 bg-amber-500 text-white rounded-full p-1 shadow-md opacity-0 scale-50 transition-all duration-200 checkmark">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" class="hidden">
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Step 2: Date & Time -->
            <div id="step2" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8 hidden transition-all duration-500 opacity-0 translate-y-4">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 text-sm font-bold">2</span>
                    Choose Date & Time
                </h2>
                
                <div class="mb-6">
                    <label for="dateInput" class="block mb-2 text-sm font-medium text-slate-900">Select Date</label>
                    <input type="date" id="dateInput" name="date" min="{{ date('Y-m-d') }}" onchange="fetchSlots()" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                </div>

                <label class="block mb-2 text-sm font-medium text-slate-900">Available Time Slots</label>
                <div id="slotsContainer" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 max-h-60 overflow-y-auto p-1 custom-scrollbar">
                    <div class="col-span-full text-center text-slate-400 py-4 text-sm">Select a date to view slots</div>
                </div>
                <input type="hidden" name="time" id="timeInput">
            </div>

            <!-- Step 3: Details -->
            <div id="step3" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8 hidden transition-all duration-500 opacity-0 translate-y-4">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 text-sm font-bold">3</span>
                    Your Details
                </h2>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="customer_name" class="block mb-2 text-sm font-medium text-slate-900">Full Name</label>
                        <input type="text" id="customer_name" name="customer_name" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="John Doe" required>
                    </div>
                    <div>
                        <label for="customer_email" class="block mb-2 text-sm font-medium text-slate-900">Email Address</label>
                        <input type="email" id="customer_email" name="customer_email" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="name@example.com" required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="customer_phone" class="block mb-2 text-sm font-medium text-slate-900">Phone Number (Optional)</label>
                        <input type="tel" id="customer_phone" name="customer_phone" class="bg-gray-50 border border-gray-300 text-slate-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="(555) 123-4567">
                    </div>
                </div>
            </div>

            <div class="h-24"></div>

            <div id="footer" class="fixed bottom-0 left-0 z-50 w-full h-20 bg-white border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] transform translate-y-full transition-transform duration-300 flex items-center justify-center">
                <div class="w-full max-w-3xl px-4 flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-sm text-slate-500">Total (<span id="totalDuration">0</span> min)</span>
                        <span class="text-2xl font-bold text-slate-900">{{ $shop->currency ?? '$' }} <span id="totalPrice">0.00</span></span>
                    </div>
                    <button type="submit" id="bookBtn" class="text-white bg-slate-900 hover:bg-slate-800 focus:ring-4 focus:ring-slate-300 font-medium rounded-full text-lg px-8 py-2.5 focus:outline-none shadow-lg hover:-translate-y-0.5 transition-transform flex items-center gap-2">
                        <span>Confirm Booking</span>
                        <svg id="btnLoader" class="hidden w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>

        </form>
    </div>

    <script>
        let selectedServices = new Set();
        let totalP = 0;
        let totalD = 0;

        function toggleService(el, id, price, duration) {
            const checkbox = el.querySelector('input');
            const checkmark = el.querySelector('.checkmark');
            
            if (selectedServices.has(id)) {
                selectedServices.delete(id);
                el.classList.remove('border-amber-500', 'bg-amber-50');
                el.classList.add('border-gray-100');
                checkmark.classList.remove('opacity-100', 'scale-100');
                checkmark.classList.add('opacity-0', 'scale-50');
                
                checkbox.checked = false;
                totalP -= price;
                totalD -= duration;
            } else {
                selectedServices.add(id);
                el.classList.remove('border-gray-100');
                el.classList.add('border-amber-500', 'bg-amber-50');
                checkmark.classList.remove('opacity-0', 'scale-50');
                checkmark.classList.add('opacity-100', 'scale-100');
                
                checkbox.checked = true;
                totalP += price;
                totalD += duration;
            }

            updateSummary();
        }

        function updateSummary() {
            document.getElementById('totalPrice').textContent = totalP.toFixed(2);
            document.getElementById('totalDuration').textContent = totalD;
            
            const footer = document.getElementById('footer');
            const step2 = document.getElementById('step2');
            const step3 = document.getElementById('step3');

            if (selectedServices.size > 0) {
                footer.classList.remove('translate-y-full');
                step2.classList.remove('hidden');
                setTimeout(() => {
                    step2.classList.remove('opacity-0', 'translate-y-4');
                }, 10);
            } else {
                footer.classList.add('translate-y-full');
                step2.classList.add('opacity-0', 'translate-y-4');
                step3.classList.add('opacity-0', 'translate-y-4');
                setTimeout(() => {
                    step2.classList.add('hidden');
                    step3.classList.add('hidden');
                }, 500);
            }
            
            if (document.getElementById('dateInput').value) {
                fetchSlots();
            }
        }

        async function fetchSlots() {
            const date = document.getElementById('dateInput').value;
            if (!date) return;

            const container = document.getElementById('slotsContainer');
            container.innerHTML = '<div class="col-span-full text-center py-4"><svg class="inline w-8 h-8 text-gray-200 animate-spin fill-amber-500" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg></div>';
            
            let baseUrl = window.location.href.split('?')[0];
            baseUrl = baseUrl.replace(/\/$/, '');
            const url = baseUrl + '/slots?date=' + date + '&duration=' + totalD;
            
            try {
                const res = await fetch(url);
                const data = await res.json();
                
                container.innerHTML = '';
                if (data.slots.length === 0) {
                    container.innerHTML = '<div class="col-span-full text-center text-red-500 py-4">No slots available for this duration.</div>';
                } else {
                    data.slots.forEach(time => {
                        const div = document.createElement('div');
                        div.className = 'py-3 px-2 text-center bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer text-sm font-semibold transition-colors border border-transparent';
                        div.textContent = time;
                        div.onclick = () => selectTime(div, time);
                        container.appendChild(div);
                    });
                }
            } catch (e) {
                console.error(e);
                container.innerHTML = '<div class="col-span-full text-center text-red-500 py-4">Error loading slots.</div>';
            }
        }

        function selectTime(el, time) {
            // Remove previous selection styles
            const allSlots = document.querySelectorAll('#slotsContainer > div');
            allSlots.forEach(d => {
                d.classList.remove('bg-amber-500', 'text-white', 'hover:bg-amber-600');
                d.classList.add('bg-gray-100', 'hover:bg-gray-200');
            });

            // Add new selection styles
            el.classList.remove('bg-gray-100', 'hover:bg-gray-200');
            el.classList.add('bg-amber-500', 'text-white', 'hover:bg-amber-600');
            
            document.getElementById('timeInput').value = time;
            
            document.getElementById('step3').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('step3').classList.remove('opacity-0', 'translate-y-4');
                document.getElementById('step3').scrollIntoView({behavior: 'smooth', block: 'start'});
            }, 10);
        }

        async function submitBooking(e) {
            e.preventDefault();
            const btn = document.getElementById('bookBtn');
            const loader = document.getElementById('btnLoader');
            
            btn.disabled = true;
            loader.classList.remove('hidden');
            
            const formData = new FormData(e.target);
            
            try {
                const res = await fetch(window.location.href.split('?')[0], { 
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await res.json();
                
                if (data.success) {
                    alert('Booking Confirmed! ID: ' + data.booking_id);
                    location.reload();
                } else {
                    alert('Error: ' + JSON.stringify(data.errors || 'Unknown error'));
                }
            } catch (err) {
                alert('Request failed');
            } finally {
                btn.disabled = false;
                loader.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
