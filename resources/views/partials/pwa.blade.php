<!-- PWA Settings -->
@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    
    // Kiosk and Public booking pages get shop-specific colors
    $isShopView = in_array($currentRoute, [
        'shop.index', 'booking.index', 'shop.my_appointments', 'booking.my_appointments', 
        'shop.kiosk', 'booking.kiosk'
    ]) || str_contains($currentRoute, 'booking.') || str_contains($currentRoute, 'shop.');
    
    $isKioskView = in_array($currentRoute, ['shop.kiosk', 'booking.kiosk']);
    
    // Dashboard and Admin strictly use brand blue.
    $pwaThemeColor = ($isShopView && isset($shop)) ? ($shop->primary_color ?? '#4896bf') : '#4896bf';
@endphp

<meta name="theme-color" content="{{ $pwaThemeColor }}">
<link rel="manifest" href="/manifest.webmanifest?v=2">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="BarberShoppe">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">

@if(!$isKioskView)
<!-- Mobile App Splash Screen -->
<div id="app-splash" style="position:fixed;top:0;left:0;width:100%;height:100%;background:#ffffff;z-index:99999;display:flex;flex-direction:column;align-items:center;justify-content:center;transition:opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.6s;">
    <div style="display:flex;flex-direction:column;align-items:center;gap:32px;">        
        <div style="width:140px;height:3px;background:#f1f5f9;border-radius:10px;overflow:hidden;position:relative;">
            <div style="position:absolute;top:0;left:0;height:100%;width:45%;background:{{ $pwaThemeColor }};border-radius:10px;animation:splashProgress 1.5s infinite cubic-bezier(0.65, 0.815, 0.735, 0.395)"></div>
        </div>
    </div>
</div>
@else
<!-- Cinematic Kiosk Splash (TV Optimized) -->
<div id="app-splash" style="position:fixed;top:0;left:0;width:100%;height:100%;background:#020617;z-index:99999;display:flex;align-items:center;justify-content:center;transition:opacity 0.8s ease-in-out, visibility 0.8s;">
    <div style="display:flex;flex-direction:column;align-items:center;gap:24px;">
        <div style="width:300px;height:2px;background:rgba(255,255,255,0.05);border-radius:10px;overflow:hidden;position:relative;">
            <div style="position:absolute;top:0;left:0;height:100%;width:40%;background:{{ $pwaThemeColor }};border-radius:10px;animation:splashProgress 2s infinite ease-in-out;box-shadow:0 0 20px {{ $pwaThemeColor }}"></div>
        </div>
    </div>
</div>
@endif

<link rel="apple-touch-icon" href="/pwa-192x192.png">
<link rel="manifest" href="/manifest.webmanifest">

<style>
    @keyframes splashProgress { 0% { left: -40%; } 100% { left: 100%; } }
</style>

@if(!$isKioskView)
<!-- PWA Install Banner -->
<div id="pwa-install-banner" class="fixed bottom-6 left-6 right-6 md:left-auto md:w-96 bg-slate-900/95 backdrop-blur-xl text-white p-4 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/10 z-[100] transition-all duration-700 ease-out flex items-center gap-4 opacity-0 translate-y-10" style="display: none;">
    <div class="w-12 h-12 bg-[#4896bf] rounded-xl flex items-center justify-center shrink-0 shadow-lg" style="background-color: {{ $pwaThemeColor }}">
        <img src="/pwa-192x192.png" alt="Icon" class="w-8 h-8">
    </div>
    <div class="flex-1">
        <h4 class="text-sm font-bold">Install App</h4>
        <p class="text-[10px] text-slate-400">Add to home screen for the best experience.</p>
    </div>
    <div class="flex gap-2">
        <button id="pwa-ignore" class="p-2 text-slate-400 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <button id="pwa-install" class="text-white text-[11px] font-black uppercase tracking-widest px-4 py-2 rounded-lg transition-all active:scale-95 shadow-lg" style="background-color: {{ $pwaThemeColor }}">
            Install
        </button>
    </div>
</div>

<!-- Push Notification Banner -->
<div id="push-notification-banner" class="fixed bottom-40 left-6 right-6 md:left-auto md:w-96 bg-slate-900/95 backdrop-blur-xl text-white p-4 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/10 z-[110] transition-all duration-700 ease-out flex items-center gap-4 opacity-0 translate-y-10" style="display: none;">
    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center shrink-0 shadow-lg">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
    </div>
    <div class="flex-1">
        <h4 class="text-sm font-bold">Stay Updated</h4>
        <p class="text-[10px] text-slate-400">Get free notifications for appointment status.</p>
    </div>
    <div class="flex gap-2">
        <button id="push-ignore" class="p-2 text-slate-400 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <button id="push-enable" class="text-white text-[11px] font-black uppercase tracking-widest px-4 py-2 rounded-lg transition-all bg-indigo-600 active:scale-95 shadow-lg">
            Enable
        </button>
    </div>
</div>
@endif

<div id="pwa-offline-indicator" class="fixed top-4 left-1/2 -translate-x-1/2 bg-rose-600/90 backdrop-blur-md text-white px-6 py-2 rounded-full text-xs font-bold shadow-2xl z-[120] opacity-0 pointer-events-none transform -translate-y-4 transition-all duration-500 flex items-center gap-2">
    Offline Mode Active
</div>

<script>
    // Global PWA Variables
    window.pwaDeferredPrompt = null;

    // Capture the install prompt as soon as it happens (often before 'load')
    window.addEventListener('beforeinstallprompt', (e) => {
        console.log('PWA: beforeinstallprompt intercepted [Event Fired]');
        e.preventDefault();
        window.pwaDeferredPrompt = e;
        
        // If the window is already loaded, show the banner immediately
        if (document.readyState === 'complete') {
            showInstallAppBanner();
        }
    });

    window.addEventListener('load', () => {
        // 1. Clear Splash
        const splash = document.getElementById('app-splash');
        if (splash) {
            splash.style.opacity = '0';
            setTimeout(() => splash.remove(), 600);
        }

        // 2. Register Service Worker at Root
        if ('serviceWorker' in navigator) {
            const swConfig = { scope: '/' };
            const swUrl = '/sw.js?v=' + Date.now(); // Cache buster for dev reliability
            
            console.log('PWA: Registering fresh SW...');
            
            // Try module registration first (standard for modern Vite)
            navigator.serviceWorker.register(swUrl, { ...swConfig, type: 'module' })
                .then(reg => {
                    console.log('PWA: Service Worker Active [Module mode]');
                    initPushSync(reg);
                })
                .catch(err => {
                    console.warn('PWA: Module registration failed, trying Classic...', err);
                    navigator.serviceWorker.register(swUrl, swConfig)
                        .then(reg => {
                            console.log('PWA: Service Worker Active [Classic mode]');
                            initPushSync(reg);
                        })
                        .catch(err2 => {
                            console.error('PWA: Total Registration Failure', err2);
                        });
                });
        }

        // 3. Persistent UI Check
        showInstallAppBanner();
    });

    function showInstallAppBanner() {
        const banner = document.getElementById('pwa-install-banner');
        if (!banner || localStorage.getItem('pwa-ignored')) return;

        console.log('PWA: INSTALL BANNER - Forcing Display');
        banner.style.display = 'flex';
        // Force layout
        void banner.offsetWidth;
        banner.classList.remove('opacity-0', 'translate-y-10');
        banner.classList.add('opacity-100', 'translate-y-0');

        document.getElementById('pwa-install')?.addEventListener('click', async () => {
            if (!window.pwaDeferredPrompt) {
                alert('The browser hasn\'t enabled the auto-prompt yet. \n\nCheck your address bar (look for an "Install" icon) or use the Browser Menu > "Install App".');
                return;
            }
            window.pwaDeferredPrompt.prompt();
            const { outcome } = await window.pwaDeferredPrompt.userChoice;
            if (outcome === 'accepted') {
                localStorage.setItem('pwa-installed', 'true');
                banner.style.display = 'none';
            }
            window.pwaDeferredPrompt = null;
        });

        document.getElementById('pwa-ignore')?.addEventListener('click', () => {
            localStorage.setItem('pwa-ignored', 'true');
            banner.style.display = 'none';
        });
    }

    function initPushSync(registration) {
        console.log('PWA: Initializing Push Sync. Permission:', Notification.permission);
        const banner = document.getElementById('push-notification-banner');
        const vapidKey = "{{ config('webpush.vapid.public_key') }}";
        const isAuth = @json(auth()->check());
        const customerPhone = "{{ $phone ?? request('phone') }}";

        if (!banner) return;

        // 1. Silent Sync (If already granted, ensure backend has it)
        if (Notification.permission === 'granted') {
            console.log('PWA: Permission granted, waiting for SW ready...');
            navigator.serviceWorker.ready.then(reg => {
                console.log('PWA: SW Ready, executing sync...');
                executeSubscription(reg, vapidKey, isAuth, customerPhone);
            });
            return;
        }

        // 2. Show Banner Logic (Context Aware)
        const showBanner = () => {
             console.log('Push: Showing Banner');
             banner.style.display = 'flex';
             // Force reflow
             banner.offsetHeight;
             banner.classList.remove('opacity-0', 'translate-y-10');
             banner.classList.add('opacity-100', 'translate-y-0');
        };

        if (Notification.permission === 'default' && !localStorage.getItem('push-ignored')) {
            if (isAuth) {
                showBanner();
            } else {
                const phoneInput = document.getElementById('customer_phone') || document.getElementById('phone');
                if (phoneInput) {
                    // If they already typed it (e.g. page reload)
                    if (phoneInput.value.length > 5) {
                        showBanner();
                    } else {
                        // Wait for them to type
                        phoneInput.addEventListener('blur', () => {
                            if (phoneInput.value.length > 5 && banner.style.display === 'none') {
                                showBanner();
                            }
                        });
                    }
                } else {
                    // No phone input found (maybe homepage), show generic prompt
                    showBanner();
                }
            }
        }

        document.getElementById('push-enable')?.addEventListener('click', async function() {
            this.disabled = true;
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                await executeSubscription(registration, vapidKey, isAuth, customerPhone);
                banner.style.display = 'none';
            } else {
                alert('Notification permission denied. Please reset via your browser settings (Lock icon).');
                this.classList.add('bg-rose-600');
                this.textContent = 'Blocked';
            }
        });

        document.getElementById('push-ignore')?.addEventListener('click', () => {
            localStorage.setItem('push-ignored', 'true');
            banner.style.display = 'none';
        });
    }

    async function executeSubscription(registration, vapidKey, isAuth, customerPhone) {
        try {
            // 1. Force Fresh Subscription (Nuclear Option to fix stale keys)
            const existingSub = await registration.pushManager.getSubscription();
            if (existingSub) {
                console.log('PWA: Unsubscribing old subscription to ensure freshness...');
                await existingSub.unsubscribe();
            }

            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidKey)
            });

            // 2. Identify User
            if (!customerPhone && !isAuth) {
                const input = document.getElementById('customer_phone') || document.getElementById('phone');
                customerPhone = input?.value;
            }

            let pushUrl = "";
            
            // 1. If Authenticated (Admin/Owner), ALWAYS use the global endpoint
            if (isAuth) {
                pushUrl = "{{ route('push.global.update') }}";
            } 
            // 2. If Shop Context (Customer), use shop-specific endpoint
            else if ("{{ request()->routeIs('shop.*') || request()->routeIs('booking.*') }}" === "1") {
                @if(request()->routeIs('shop.*'))
                    pushUrl = "{{ route('push.update') }}";
                @elseif(isset($shop))
                    pushUrl = "/book/{{ $shop->slug }}/push-subscriptions";
                @endif
            }
            // 3. Fallback for public booking pages using slug
            else {
                @if(isset($shop))
                    pushUrl = "/book/{{ $shop->slug }}/push-subscriptions";
                @else
                   pushUrl = "{{ route('push.global.update') }}";
                @endif
            }

            if (customerPhone || isAuth) {
                try {
                    console.log('PWA: Sending subscription to backend...', pushUrl);
                    const response = await fetch(pushUrl, {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': "{{ csrf_token() }}", 
                            'Accept': 'application/json' 
                        },
                        body: JSON.stringify({ ...subscription.toJSON(), phone: customerPhone })
                    });
                    
                    if (!response.ok) throw new Error('Backend rejected subscription');
                    console.log('PWA: Subscription Synced Successfully');
                    
                    // Optional: Visual Confirmation
                    /*
                    const toast = document.createElement('div');
                    toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm font-medium animate-bounce';
                    toast.textContent = 'Notifications Connected';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                    */

                } catch (err) {
                    console.error('PWA: Sync Failed', err);
                    alert('Could not connect to notification server. Please refresh.');
                }
            } else {
                localStorage.setItem('pending-push-sub', JSON.stringify(subscription));
            }
        } catch (e) { 
            console.error('Push Subscription Failed', e); 
            // If subscription fails (e.g. valid key issues), show the banner so they can try again manually
            const banner = document.getElementById('push-notification-banner');
            if(banner) {
                banner.style.display = 'flex';
                banner.classList.remove('opacity-0', 'translate-y-10');
            }
        }
    }

    // Listener for late-entry of phone number (to sync pending subscriptions)
    document.addEventListener('DOMContentLoaded', () => {
        const phoneInputs = ['customer_phone', 'phone'];
        
        phoneInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('blur', async () => {
                    const phone = input.value;
                    const pendingSub = localStorage.getItem('pending-push-sub');
                    
                    if (phone && pendingSub) {
                        console.log('PWA: Found pending subscription and phone number, syncing...');
                        const subscription = JSON.parse(pendingSub);
                        const isAuth = {{ auth()->check() ? 'true' : 'false' }};
                        
                        // Re-use logic to define URL (Ideally refactor this, but for now copying ensures scoping)
                        let pushUrl = "";
                        if (isAuth) {
                            pushUrl = "{{ route('push.global.update') }}";
                        } else if ("{{ request()->routeIs('shop.*') || request()->routeIs('booking.*') }}" === "1") {
                            @if(request()->routeIs('shop.*'))
                                pushUrl = "{{ route('push.update') }}";
                            @elseif(isset($shop))
                                pushUrl = "/book/{{ $shop->slug }}/push-subscriptions";
                            @else
                                pushUrl = "{{ route('push.global.update') }}";
                            @endif
                        } else {
                             @if(isset($shop))
                                pushUrl = "/book/{{ $shop->slug }}/push-subscriptions";
                            @else
                                pushUrl = "{{ route('push.global.update') }}";
                            @endif
                        }

                        try {
                            const response = await fetch(pushUrl, {
                                method: 'POST',
                                headers: { 
                                    'Content-Type': 'application/json', 
                                    'X-CSRF-TOKEN': "{{ csrf_token() }}", 
                                    'Accept': 'application/json' 
                                },
                                body: JSON.stringify({ ...subscription, phone: phone })
                            });
                            
                            if (response.ok) {
                                console.log('PWA: Pending Subscription Synced Successfully');
                                localStorage.removeItem('pending-push-sub');
                            }
                        } catch (err) {
                            console.error('PWA: Retry Sync Failed', err);
                        }
                    }
                });
            }
        });
    });

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
        return outputArray;
    }
</script>
