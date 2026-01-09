<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shop->name }} - Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --primary: {{ $shop->primary_color ?? '#c5a059' }};
            --bg-dark: #0f172a;
            --bg-card: rgba(30, 41, 59, 0.7);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: {{ $shop->primary_color ?? '#38bdf8' }};
            --glass: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
        }

        .kiosk-container {
            display: grid;
            grid-template-rows: auto 1fr auto;
            height: 100vh;
            padding: 2rem;
            gap: 2rem;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--glass);
            backdrop-filter: blur(12px);
            padding: 1.5rem 2.5rem;
            border-radius: 24px;
            border: 1px solid var(--border);
        }

        .shop-info h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .clock-container {
            text-align: right;
        }

        #clock {
            font-size: 3rem;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        #date {
            color: var(--text-muted);
            font-size: 1.2rem;
        }

        /* Main Content */
        main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            overflow: hidden;
        }

        .section-card {
            background: var(--bg-card);
            border-radius: 32px;
            padding: 2rem;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Stylists Grid */
        .stylist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .stylist-card {
            background: var(--glass);
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s ease;
            border: 1px solid var(--border);
        }

        .stylist-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            object-fit: cover;
            border: 3px solid var(--primary);
            padding: 3px;
        }

        .stylist-name {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-available { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .status-busy { background: rgba(239, 68, 68, 0.2); color: #f87171; }

        /* Queue Section */
        .queue-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .queue-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--glass);
            padding: 1.25rem 1.5rem;
            border-radius: 16px;
            border-left: 4px solid var(--primary);
        }

        .queue-time {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .queue-service {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Footer / Booking QR */
        footer {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 2rem;
            align-items: center;
            background: var(--glass);
            padding: 1.5rem;
            border-radius: 24px;
            border: 1px solid var(--border);
        }

        .qr-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        #qrcode {
            background: white;
            padding: 10px;
            border-radius: 12px;
        }

        .qr-text h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .qr-text p {
            color: var(--text-muted);
        }

        .services-ticker-container {
            overflow: hidden;
            white-space: nowrap;
            position: relative;
        }

        .services-ticker {
            display: inline-block;
            animation: ticker 30s linear infinite;
        }

        .ticker-item {
            display: inline-flex;
            align-items: center;
            margin-right: 3rem;
            font-size: 1.2rem;
            font-weight: 500;
        }

        .ticker-price {
            color: var(--primary);
            margin-left: 0.5rem;
            font-weight: 700;
        }

        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        .now-serving-badge {
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            display: inline-block;
        }

        /* Fullscreen Toggle */
        .fullscreen-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            color: var(--text-main);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }

        .fullscreen-toggle:hover {
            transform: scale(1.1) rotate(5deg);
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 15px 30px -10px rgba(197, 160, 89, 0.5);
        }

        .fullscreen-toggle:active {
            transform: scale(0.95);
        }

        .fullscreen-toggle svg {
            width: 24px;
            height: 24px;
        }

        /* Tooltip */
        .fullscreen-toggle::after {
            content: 'Toggle Fullscreen';
            position: absolute;
            right: 120%;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            transform: translateX(10px);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .fullscreen-toggle:hover::after {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</head>
<body>
    <div class="kiosk-container">
        <header>
            <div class="shop-info" style="display: flex; align-items: center; gap: 1.5rem;">
                @if($shop->logo)
                    <img src="{{ $shop->logo }}" alt="{{ $shop->name }}" style="height: 60px; width: auto; object-contain; border-radius: 8px;">
                @endif
                <h1>{{ $shop->name }}</h1>
            </div>
            <div class="clock-container">
                <div id="clock">00:00:00</div>
                <div id="date">Thursday, January 8</div>
            </div>
        </header>

        <main>
            <div class="section-card">
                <div class="section-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Our Stylists
                </div>
                <div class="stylist-grid">
                    @foreach($stylists as $stylist)
                    <div class="stylist-card">
                        @if($stylist->image_base64)
                            <img src="{{ $stylist->image_base64 }}" alt="{{ $stylist->name }}" class="stylist-image">
                        @else
                            <div class="stylist-image" style="background: #1e293b; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--primary);">
                                {{ substr($stylist->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="stylist-name">{{ $stylist->name }}</div>
                        @php
                            $isBusy = $nowServing->contains('stylist_id', $stylist->id);
                        @endphp
                        <span class="status-badge {{ $isBusy ? 'status-busy' : 'status-available' }}">
                            {{ $isBusy ? 'Busy' : 'Available' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="section-card">
                <div class="section-title">
                    Now Serving & Next Up
                </div>
                <div class="queue-list">
                    @forelse($nowServing as $booking)
                        <div class="queue-item" style="background: {{ $booking->status === 'in_progress' ? 'rgba(56, 189, 248, 0.15)' : 'rgba(197, 160, 89, 0.1)' }}; border-color: {{ $booking->status === 'in_progress' ? 'var(--accent)' : 'var(--primary)' }};">
                            <div>
                                <span class="now-serving-badge" style="background: {{ $booking->status === 'in_progress' ? 'var(--accent)' : 'var(--primary)' }};">
                                    {{ $booking->status === 'in_progress' ? 'IN PROGRESS' : 'NOW SERVING' }}
                                </span>
                                <div class="queue-time">{{ $booking->customer->name }}</div>
                                <div class="queue-service">with {{ $booking->stylist->name }}</div>
                            </div>
                            <div class="queue-time" style="color: var(--accent);">{{ $booking->start_time->format('H:i') }}</div>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-muted); padding: 1rem;">
                            No active sessions
                        </div>
                    @endforelse

                    <div style="margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.1em;">Next Up</div>
                    
                    @forelse($nextUp as $booking)
                        <div class="queue-item">
                            <div>
                                <div class="queue-time">{{ $booking->customer->name }}</div>
                                <div class="queue-service">with {{ $booking->stylist->name }}</div>
                            </div>
                            <div class="queue-time">{{ $booking->start_time->format('H:i') }}</div>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-muted); padding: 1rem;">
                            No upcoming bookings
                        </div>
                    @endforelse
                </div>
            </div>
        </main>

        <footer>
            <div class="qr-section">
                <div id="qrcode"></div>
                <div class="qr-text">
                    <h2>Book Your Spot</h2>
                    <p>Scan to see available slots and book online</p>
                </div>
            </div>
            <div class="services-ticker-container">
                <div class="services-ticker" id="ticker">
                    @foreach($services as $service)
                        <div class="ticker-item">
                            {{ $service->name }} <span class="ticker-price">{{ $shop->currency ?? '$' }} {{ number_format($service->price, 2) }}</span>
                        </div>
                    @endforeach
                    {{-- Duplicate for seamless loop --}}
                    @foreach($services as $service)
                        <div class="ticker-item">
                            {{ $service->name }} <span class="ticker-price">{{ $shop->currency ?? '$' }} {{ number_format($service->price, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </footer>
    </div>

    <button id="fullscreen-btn" class="fullscreen-toggle" aria-label="Toggle Fullscreen">
        <svg id="fs-icon-maximize" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
        <svg id="fs-icon-minimize" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"/></svg>
    </button>

    <script>
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { 
                hour12: false, 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                timeZone: "{{ $shop->timezone ?? config('app.timezone') }}"
            });
            const dateStr = now.toLocaleDateString('en-US', { 
                weekday: 'long', 
                month: 'long', 
                day: 'numeric',
                timeZone: "{{ $shop->timezone ?? config('app.timezone') }}"
            });
            
            document.getElementById('clock').textContent = timeStr;
            document.getElementById('date').textContent = dateStr;
        }

        setInterval(updateClock, 1000);
        updateClock();

        // Generate QR Code
        const bookingUrl = "{{ route('booking.via_slug', ['slug' => $shop->slug]) }}";
        new QRCode(document.getElementById("qrcode"), {
            text: bookingUrl,
            width: 120,
            height: 120,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Auto refresh page every 20 seconds to update queue
        setTimeout(() => {
            window.location.reload();
        }, 20000);

        // Fullscreen Logic
        const fsBtn = document.getElementById('fullscreen-btn');
        const maxIcon = document.getElementById('fs-icon-maximize');
        const minIcon = document.getElementById('fs-icon-minimize');

        function updateFsIcons() {
            if (document.fullscreenElement) {
                maxIcon.style.display = 'none';
                minIcon.style.display = 'block';
                fsBtn.setAttribute('title', 'Exit Fullscreen');
            } else {
                maxIcon.style.display = 'block';
                minIcon.style.display = 'none';
                fsBtn.setAttribute('title', 'Enter Fullscreen');
            }
        }

        fsBtn.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error(`Error attempting to enable full-screen mode: ${err.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        });

        document.addEventListener('fullscreenchange', updateFsIcons);

        // Check for auto-fullscreen request
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('fullscreen') === '1') {
            // Create a temporary overlay to request fullscreen on first click
            const overlay = document.createElement('div');
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100vw';
            overlay.style.height = '100vh';
            overlay.style.background = 'rgba(0,0,0,0.8)';
            overlay.style.color = 'white';
            overlay.style.display = 'flex';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.zIndex = '9999';
            overlay.style.cursor = 'pointer';
            overlay.innerHTML = '<div style="text-align:center"><h2 style="font-size:2rem;margin-bottom:1rem">Kiosk Mode</h2><p>Click anywhere to enter full screen</p></div>';
            
            overlay.onclick = () => {
                document.documentElement.requestFullscreen();
                overlay.remove();
            };
            document.body.appendChild(overlay);
        }
    </script>
</body>
</html>
