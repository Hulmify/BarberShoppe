<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $shop->name }} - Kiosk</title>
    @if($shop->logo)
        <link rel="icon" type="image/png" href="{{ $shop->logo }}">
    @else
        <link rel="icon" type="image/png" href="/app_favicon.png">
    @endif
    @include('partials.pwa')
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --primary: {{ $shop->primary_color ?? '#4896bf' }};
            --primary-rgb: {{ implode(',', sscanf($shop->primary_color ?? '#4896bf', "#%02x%02x%02x")) }};
        }

        body {
            background-color: #020617;
            color: #f8fafc;
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            font-family: 'Outfit', sans-serif;
        }

        /* Cinematic Background */
        .cinematic-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle at 10% 20%, rgba(var(--primary-rgb), 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(var(--primary-rgb), 0.1) 0%, transparent 40%),
                        #020617;
        }

        .ambient-light {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(var(--primary-rgb), 0.08) 0%, transparent 70%);
            border-radius: 50%;
            animation: drift 30s infinite alternate-reverse ease-in-out;
            pointer-events: none;
        }

        @keyframes drift {
            0% { transform: translate(-20%, -20%) scale(1); }
            100% { transform: translate(40%, 40%) scale(1.2); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .ticker-item {
            display: inline-flex;
            align-items: center;
            margin-right: 4rem;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .animate-ticker {
            display: inline-block;
            animation: ticker 40s linear infinite;
        }

        @keyframes scroll-queue {
            0% { transform: translateY(0); }
            100% { transform: translateY(-50%); }
        }

        .animate-queue {
            animation: scroll-queue 30s linear infinite;
        }

        .now-serving-item {
            background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.2), rgba(var(--primary-rgb), 0.05));
            border-left: 6px solid var(--primary);
        }

        @media (max-width: 768px) {
            body { 
                overflow: auto; 
                height: auto; 
                padding: 1rem;
                gap: 1rem;
            }
            .animate-queue { animation: none; transform: none; }
            .glass-card { 
                padding: 1.5rem !important; 
                border-radius: 20px !important; 
            }
            header {
                flex-direction: column;
                text-align: center;
                gap: 1.5rem;
            }
            header .flex-items-center {
                flex-direction: column;
                gap: 1rem;
            }
            header h1 {
                font-size: 2.5rem !important;
            }
            header #clock {
                font-size: 4rem !important;
            }
            header .text-right {
                text-align: center !important;
            }
        }
    </style>
</head>
<body class="flex flex-col h-screen md:p-6 p-4 gap-4 md:gap-6 relative">
    <div class="cinematic-bg">
        <div class="ambient-light" style="top: -10%; left: -10%;"></div>
        <div class="ambient-light" style="bottom: -10%; right: -10%; animation-delay: -15s;"></div>
    </div>

    <!-- Header -->
    <header class="glass-card rounded-[32px] md:p-8 p-6 flex flex-col md:flex-row justify-between items-center animate-fade-in-down gap-6 md:gap-0">
        <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
            @if($shop->logo)
                <img src="{{ $shop->logo }}" alt="{{ $shop->name }}" class="h-16 md:h-20 w-auto object-contain rounded-2xl bg-white/10 p-2 border border-white/10">
            @else
                <div class="h-16 w-16 md:h-20 md:w-20 rounded-2xl flex items-center justify-center text-3xl md:text-4xl font-black shadow-2xl" style="background: var(--primary)">
                    {{ substr($shop->name, 0, 1) }}
                </div>
            @endif
            <div class="text-center md:text-left">
                <h1 class="text-3xl md:text-5xl font-black uppercase tracking-tighter" style="color: var(--primary)">{{ $shop->name }}</h1>
                <p class="text-slate-400 font-medium tracking-widest uppercase text-xs md:text-sm mt-1">Queue Board</p>
            </div>
        </div>
        <div class="text-center md:text-right">
            <div id="clock" class="text-5xl md:text-7xl font-black tracking-tighter tabular-nums leading-none">00:00</div>
            <div id="date" class="text-lg md:text-xl font-bold text-slate-500 uppercase tracking-widest mt-2">JANUARY 24</div>
        </div>
    </header>

    <!-- Main Grid -->
    <main class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8 min-h-0">
        <!-- Stylists Section -->
        <section class="glass-card rounded-[32px] md:rounded-[40px] p-6 md:p-10 flex flex-col min-h-0">
            <h2 class="text-[10px] md:text-xs font-black uppercase tracking-[0.4em] text-slate-500 mb-6 md:mb-8 flex items-center gap-3">
                <span class="w-6 md:w-8 h-px bg-slate-800"></span>
                Our Professionals
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 md:gap-6 overflow-y-auto pr-2 custom-scrollbar">
                @foreach($stylists as $stylist)
                    @php $isBusy = $nowServing->contains('stylist_id', $stylist->id); @endphp
                    <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 text-center transition-all duration-500 hover:scale-105 {{ $isBusy ? 'opacity-40 grayscale-[0.5]' : '' }}">
                        <div class="relative inline-block mb-3 md:mb-4">
                            @if($stylist->image_base64)
                                <img src="{{ $stylist->image_base64 }}" class="w-16 h-16 md:w-24 md:h-24 rounded-full object-cover border-2 md:border-4" style="border-color: {{ $isBusy ? '#334155' : 'var(--primary)' }}">
                            @else
                                <div class="w-16 h-16 md:w-24 md:h-24 rounded-full bg-slate-800 flex items-center justify-center text-xl md:text-3xl font-bold border-2 md:border-4 border-slate-700 text-slate-500">
                                    {{ substr($stylist->name, 0, 1) }}
                                </div>
                            @endif
                            <!-- Pulse indicator -->
                            <span class="absolute bottom-1 right-1 w-3 h-3 md:w-5 md:h-5 rounded-full border-2 md:border-4 border-[#101827] {{ $isBusy ? 'bg-rose-500' : 'bg-green-500 shadow-[0_0_15px_rgba(34,197,94,0.5)]' }}"></span>
                        </div>
                        <div class="text-sm md:text-lg font-black tracking-tight leading-tight">{{ $stylist->name }}</div>
                        <div class="text-[8px] md:text-[10px] font-black uppercase tracking-widest mt-1 {{ $isBusy ? 'text-slate-500' : 'text-green-500' }}">
                            {{ $isBusy ? 'Currently Busy' : 'Available Now' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Queue Section -->
        <section class="glass-card rounded-[32px] md:rounded-[40px] p-6 md:p-10 flex flex-col min-h-0 overflow-hidden">
            <h2 class="text-[10px] md:text-xs font-black uppercase tracking-[0.4em] text-slate-500 mb-6 md:mb-8 flex items-center gap-3">
                <span class="w-6 md:w-8 h-px bg-slate-800"></span>
                Now Serving & Next Up
            </h2>
            <div class="flex-1 overflow-hidden relative mt-2 md:mt-4" id="queue-container">
                <div id="queue-wrapper" class="flex flex-col gap-6 md:gap-8 pr-4">
                    <div class="flex flex-col gap-6 md:gap-8" id="queue-scroll">
                    <!-- Serving -->
                    <div class="flex flex-col gap-4">
                        @forelse($nowServing as $booking)
                            <div class="now-serving-item glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 flex justify-between items-center">
                                <div>
                                    <div class="text-[8px] md:text-xs font-black uppercase tracking-[0.2em] text-white/40 mb-1">Serving</div>
                                    <div class="text-xl md:text-3xl font-black tracking-tighter">{{ $booking->customer->name }}</div>
                                    <div class="text-xs md:text-sm font-bold text-white/50">with {{ $booking->stylist->name }}</div>
                                </div>
                                <div class="text-2xl md:text-4xl font-black tabular-nums" style="color: var(--primary)">{{ $booking->start_time->setTimezone($shop->timezone ?? config('app.timezone'))->format('h:i A') }}</div>
                            </div>
                        @empty
                            <div class="p-6 md:p-8 text-center glass-card rounded-2xl md:rounded-3xl border-dashed border-slate-700/50">
                                <p class="text-slate-500 font-bold uppercase tracking-widest text-[10px] md:text-sm text-balance">Welcoming New Walk-ins</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Next -->
                    @if($nextUp->count() > 0)
                    <div class="flex flex-col gap-4">
                        <h2 class="text-[10px] md:text-xs font-black uppercase tracking-[0.4em] text-slate-500 mb-2 flex items-center gap-3">
                            <span class="w-6 md:w-8 h-px bg-slate-800"></span>
                            Next Appointments
                        </h2>
                        @foreach($nextUp as $booking)
                            <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-6 flex justify-between items-center">
                                <div>
                                    <div class="text-lg md:text-2xl font-black text-slate-300 tracking-tighter">{{ $booking->customer->name }}</div>
                                    <div class="text-[10px] md:text-xs font-bold text-slate-500">with {{ $booking->stylist->name }}</div>
                                </div>
                                <div class="text-xl md:text-3xl font-black text-slate-400 tabular-nums">{{ $booking->start_time->setTimezone($shop->timezone ?? config('app.timezone'))->format('h:i A') }}</div>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Past Due -->
                    @if($pastDue->count() > 0)
                        <div class="flex flex-col gap-4">
                            <h2 class="text-[10px] md:text-xs font-black uppercase tracking-[0.4em] text-orange-500 mb-2 flex items-center gap-3">
                                <span class="animate-pulse w-1.5 md:w-2 h-1.5 md:h-2 rounded-full bg-orange-500"></span>
                                Past Due
                            </h2>
                            @foreach($pastDue as $booking)
                                <div class="glass-card rounded-2xl md:rounded-3xl p-4 md:p-5 flex justify-between items-center bg-orange-950/20 border-orange-500/30">
                                    <div>
                                        <div class="text-lg md:text-2xl font-black text-orange-200 tracking-tighter">{{ $booking->customer->name }}</div>
                                        <div class="text-[8px] md:text-[10px] font-bold text-orange-500/60 uppercase tracking-widest">Awaiting Completion</div>
                                    </div>
                                    <div class="text-xl md:text-2xl font-black text-orange-500 tabular-nums">{{ $booking->start_time->setTimezone($shop->timezone ?? config('app.timezone'))->format('h:i A') }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Completed -->
                    @if($completedToday->count() > 0)
                        <div class="flex flex-col gap-4 opacity-50">
                            <h2 class="text-[10px] md:text-xs font-black uppercase tracking-[0.4em] text-green-600 mb-2 flex items-center gap-3">
                                <span class="w-6 md:w-8 h-px bg-green-900/30"></span>
                                Completed Today
                            </h2>
                            @foreach($completedToday as $booking)
                                <div class="glass-card rounded-2xl md:rounded-3xl p-3 md:p-4 flex justify-between items-center bg-green-900/10 border-green-900/20">
                                    <div>
                                        <div class="text-lg md:text-xl font-bold text-green-500/80 line-through tracking-tighter">{{ $booking->customer->name }}</div>
                                        <div class="text-[8px] md:text-[10px] font-bold text-green-600 uppercase tracking-widest">Finished</div>
                                    </div>
                                    <div class="text-lg md:text-xl font-bold text-green-700 tabular-nums">{{ $booking->start_time->setTimezone($shop->timezone ?? config('app.timezone'))->format('h:i A') }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="glass-card rounded-[32px] p-4 md:p-6 flex flex-col md:flex-row items-center gap-6 md:gap-10 overflow-hidden">
        <div class="flex items-center gap-4 md:gap-6 shrink-0 md:border-r border-white/10 md:pr-10 w-full md:w-auto justify-center md:justify-start">
            <div id="qrcode" class="bg-white p-1.5 md:p-2 rounded-xl md:rounded-2xl shadow-2xl"></div>
            <div>
                <h3 class="text-xl md:text-2xl font-black tracking-tighter">Book Online</h3>
                <p class="text-slate-500 text-[10px] md:text-sm font-bold uppercase tracking-widest mt-0.5 md:mt-1">Scan for Slots</p>
            </div>
        </div>
        
        <div class="flex-1 overflow-hidden w-full">
            <div class="animate-ticker whitespace-nowrap py-2 md:py-0">
                @foreach($services as $service)
                    <div class="ticker-item text-lg md:text-2xl">
                        <span class="text-slate-500 uppercase text-[8px] md:text-xs tracking-widest font-black mr-2 md:mr-3">Service</span>
                        <span class="text-white">{{ $service->name }}</span>
                        <span class="ml-2 md:ml-4 tabular-nums" style="color: var(--primary)">{{ $shop->currency ?? '$' }}{{ number_format($service->price, 2) }}</span>
                    </div>
                @endforeach
                {{-- Duplicate --}}
                @foreach($services as $service)
                    <div class="ticker-item text-lg md:text-2xl">
                        <span class="text-slate-500 uppercase text-[8px] md:text-xs tracking-widest font-black mr-2 md:mr-3">Service</span>
                        <span class="text-white">{{ $service->name }}</span>
                        <span class="ml-2 md:ml-4 tabular-nums" style="color: var(--primary)">{{ $shop->currency ?? '$' }}{{ number_format($service->price, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </footer>

    <!-- Fullscreen Action -->
    <button id="fullscreen-btn" class="fixed bottom-10 right-10 w-16 h-16 glass-card rounded-full flex items-center justify-center text-slate-400 hover:bg-white/10 transition-all opacity-20 hover:opacity-100 z-[100]">
        <svg id="fs-maximize" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
        <svg id="fs-minimize" class="w-8 h-8 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3v3a2 2 0 01-2 2H3m18 0h-3a2 2 0 01-2-2V3m0 18v-3a2 2 0 012-2h3M3 16h3a2 2 0 012 2v3"/></svg>
    </button>

    <script>
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { 
                hour12: true, 
                hour: '2-digit', 
                minute: '2-digit',
                timeZone: "{{ $shop->timezone ?? config('app.timezone') }}"
            });
            const dateStr = now.toLocaleDateString('en-US', { 
                month: 'long', 
                day: 'numeric',
                timeZone: "{{ $shop->timezone ?? config('app.timezone') }}"
            });
            
            document.getElementById('clock').textContent = timeStr;
            document.getElementById('date').textContent = dateStr.toUpperCase();
        }

        setInterval(updateClock, 1000);
        updateClock();

        // QR Code
        const qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "{{ $shop->booking_url }}",
            width: 80,
            height: 80,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Auto Refresh
        setTimeout(() => window.location.reload(), 30000);

        // Auto Dynamic Animation Speed
        window.addEventListener('load', () => {
            const queueScroll = document.getElementById('queue-scroll');
            const queueWrapper = document.getElementById('queue-wrapper');
            const queueContainer = document.getElementById('queue-container');
            
            if (queueScroll && queueWrapper && queueContainer) {
                const scrollHeight = queueScroll.offsetHeight;
                const containerHeight = queueContainer.offsetHeight;

                // Only scroll if content is taller than container plus a buffer
                if (scrollHeight > containerHeight + 20) {
                    const clones = queueScroll.cloneNode(true);
                    clones.id = 'queue-scroll-clone';
                    queueWrapper.appendChild(clones);

                    // Add animation class to the WRAPPER instead of items
                    queueWrapper.classList.add('animate-queue');

                    const duration = Math.max(30, scrollHeight / 25);
                    queueWrapper.style.animationDuration = `${duration}s`;
                }
            }
        });

        // Fullscreen Toggle
        const fsBtn = document.getElementById('fullscreen-btn');
        const maxIcon = document.getElementById('fs-maximize');
        const minIcon = document.getElementById('fs-minimize');

        fsBtn.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        });

        document.addEventListener('fullscreenchange', () => {
            if (document.fullscreenElement) {
                maxIcon.classList.add('hidden');
                minIcon.classList.remove('hidden');
            } else {
                maxIcon.classList.remove('hidden');
                minIcon.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
