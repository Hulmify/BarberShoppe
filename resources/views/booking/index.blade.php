<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $shop->name }} | Book Appointment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: {{ $shop->primary_color ?? '#10b981' }};
            --primary-fade: {{ ($shop->primary_color ?? '#10b981') }}20;
            --bg: #f8fafc;
            --text: #1e293b;
            --text-light: #64748b;
            --card-bg: #ffffff;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; outline: none; }
        body { font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; }

        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        
        /* Header */
        header { text-align: center; padding: 40px 0; animation: fadeIn 0.8s ease; }
        h1 { font-size: 2.5rem; font-weight: 700; background: linear-gradient(135deg, var(--text) 0%, var(--primary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 10px; }
        p.subtitle { color: var(--text-light); font-size: 1.1rem; }

        /* Card */
        .card { background: var(--card-bg); border-radius: 20px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); margin-bottom: 30px; transition: transform 0.3s; }
        .card:hover { transform: translateY(-2px); }

        h2 { font-size: 1.5rem; margin-bottom: 20px; color: var(--text); border-left: 4px solid var(--primary); padding-left: 15px; }

        /* Services */
        .service-list { display: grid; gap: 15px; }
        .service-item { display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.2s; }
        .service-item:hover { border-color: var(--primary-fade); background: var(--primary-fade); }
        .service-item.selected { border-color: var(--primary); background: #f0fdf4; box-shadow: 0 4px 12px var(--primary-fade); }
        .service-info h3 { font-size: 1.1rem; font-weight: 600; }
        .service-info span { font-size: 0.9rem; color: var(--text-light); }
        .service-price { font-weight: 700; color: var(--primary); font-size: 1.2rem; }
        
        /* Calendar & Slots */
        input[type="date"] { width: 100%; padding: 15px; border-radius: 12px; border: 2px solid #e2e8f0; font-family: inherit; font-size: 1rem; margin-bottom: 20px; }
        .slots-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; }
        .time-slot { padding: 10px; text-align: center; background: #e2e8f0; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
        .time-slot:hover { background: #cbd5e1; }
        .time-slot.selected { background: var(--primary); color: white; transform: scale(1.05); }

        /* Form */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-light); }
        .form-control { width: 100%; padding: 12px 15px; border-radius: 10px; border: 2px solid #e2e8f0; font-size: 1rem; transition: border-color 0.2s; }
        .form-control:focus { border-color: var(--primary); }

        /* Total Bar */
        .sticky-footer { position: fixed; bottom: 0; left: 0; width: 100%; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-top: 1px solid #e2e8f0; padding: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 -5px 20px rgba(0,0,0,0.05); transform: translateY(100%); transition: transform 0.3s; }
        .sticky-footer.visible { transform: translateY(0); }
        .total-info div { font-size: 0.9rem; color: var(--text-light); }
        .total-info strong { font-size: 1.4rem; color: var(--text); }
        
        .btn { background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 50px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 15px var(--primary-fade); }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px var(--primary-fade); }
        .btn:disabled { opacity: 0.7; cursor: not-allowed; }

        /* Loader */
        .loader { width: 20px; height: 20px; border: 3px solid #fff; border-bottom-color: transparent; border-radius: 50%; display: inline-block; box-sizing: border-box; animation: rotation 1s linear infinite; display: none; margin-left: 10px; }
        @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .hidden { display: none; }
        .error-msg { color: #ef4444; font-size: 0.9rem; margin-top: 5px; }

    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>{{ $shop->name }}</h1>
            <p class="subtitle">{{ $shop->description ?? 'Premium Barber Services' }}</p>
        </header>

        <form id="bookingForm" onsubmit="submitBooking(event)">
            
            <!-- Step 1: Services -->
            <div class="card">
                <h2>1. Select Services (Multiple)</h2>
                <div class="service-list">
                    @foreach($services as $service)
                        <div class="service-item" onclick="toggleService(this, {{ $service->id }}, {{ $service->price }}, {{ $service->duration_minutes }})">
                            <div class="service-info">
                                <h3>{{ $service->name }}</h3>
                                <span>{{ $service->duration_minutes }} min • {{ $service->description }}</span>
                            </div>
                            <div class="service-price">{{ $shop->currency ?? 'INR' }} {{ $service->price }}</div>
                            <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" class="hidden">
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Step 2: Date & Time -->
            <div class="card hidden" id="step2">
                <h2>2. Choose Date & Time</h2>
                <input type="date" id="dateInput" name="date" min="{{ date('Y-m-d') }}" onchange="fetchSlots()">
                <div id="slotsContainer" class="slots-grid"></div>
                <input type="hidden" name="time" id="timeInput">
            </div>

            <!-- Step 3: Details -->
            <div class="card hidden" id="step3">
                <h2>3. Your Details</h2>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="customer_name" class="form-control" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="customer_email" class="form-control" required placeholder="john@example.com">
                </div>
                <div class="form-group">
                    <label>Phone Number (Optional)</label>
                    <input type="tel" name="customer_phone" class="form-control" placeholder="(555) 123-4567">
                </div>
            </div>

            <div class="sticky-footer" id="footer">
                <div class="total-info">
                    <div>Total: <span id="totalDuration">0</span> min</div>
                    <strong>{{ $shop->currency ?? 'INR' }} <span id="totalPrice">0.00</span></strong>
                </div>
                <button type="submit" class="btn" id="bookBtn">
                    Confirm Booking <span class="loader" id="btnLoader"></span>
                </button>
            </div>

        </form>
    </div>

    <script>
        let selectedServices = new Set();
        let totalP = 0;
        let totalD = 0;

        function toggleService(el, id, price, duration) {
            const checkbox = el.querySelector('input');
            
            if (selectedServices.has(id)) {
                selectedServices.delete(id);
                el.classList.remove('selected');
                checkbox.checked = false;
                totalP -= price;
                totalD -= duration;
            } else {
                selectedServices.add(id);
                el.classList.add('selected');
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

            if (selectedServices.size > 0) {
                footer.classList.add('visible');
                step2.classList.remove('hidden');
            } else {
                footer.classList.remove('visible');
                step2.classList.add('hidden');
                document.getElementById('step3').classList.add('hidden');
            }
            
            // Re-fetch slots if date is selected (duration changed)
            if (document.getElementById('dateInput').value) {
                fetchSlots();
            }
        }

        async function fetchSlots() {
            const date = document.getElementById('dateInput').value;
            if (!date) return;

            const container = document.getElementById('slotsContainer');
            container.innerHTML = '<p>Loading slots...</p>';
            
            // Endpoint depends on logic, assume slug or domain handling works
            // If URL is /book/slug, we want /book/slug/slots
            // If URL is domain.com/, we want domain.com/slots
            
            let baseUrl = window.location.href.split('?')[0];
            // Remove trailing slash if present
            baseUrl = baseUrl.replace(/\/$/, '');
            
            const url = baseUrl + '/slots?date=' + date + '&duration=' + totalD;
            
            try {
                const res = await fetch(url);
                const data = await res.json();
                
                container.innerHTML = '';
                if (data.slots.length === 0) {
                    container.innerHTML = '<p>No slots available for this duration.</p>';
                } else {
                    data.slots.forEach(time => {
                        const div = document.createElement('div');
                        div.className = 'time-slot';
                        div.textContent = time;
                        div.onclick = () => selectTime(div, time);
                        container.appendChild(div);
                    });
                }
            } catch (e) {
                console.error(e);
                container.innerHTML = '<p>Error loading slots.</p>';
            }
        }

        function selectTime(el, time) {
            document.querySelectorAll('.time-slot').forEach(d => d.classList.remove('selected'));
            el.classList.add('selected');
            document.getElementById('timeInput').value = time;
            document.getElementById('step3').classList.remove('hidden');
            // Scroll to step 3
            document.getElementById('step3').scrollIntoView({behavior: 'smooth'});
        }

        async function submitBooking(e) {
            e.preventDefault();
            const btn = document.getElementById('bookBtn');
            const loader = document.getElementById('btnLoader');
            
            btn.disabled = true;
            loader.style.display = 'inline-block';
            
            const formData = new FormData(e.target);
            
            try {
                const res = await fetch(window.location.href.split('?')[0], { // Post to current URL (mapped to store) OR /book
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
                loader.style.display = 'none';
            }
        }
    </script>
</body>
</html>
