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

<!-- The manifest is handled by Vite PWA plugin -->
<link rel="manifest" href="/build/manifest.webmanifest">

<style>
    @keyframes splashPulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.9; }
    }
    @keyframes splashProgress {
        0% { left: -40%; }
        100% { left: 100%; }
    }
</style>

@if(!$isKioskView)
<!-- PWA Custom Interface -->
<div id="pwa-install-banner" class="fixed bottom-24 sm:bottom-6 left-6 right-6 md:left-auto md:w-96 bg-slate-900/95 backdrop-blur-xl text-white p-4 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/10 z-[100] opacity-0 pointer-events-none transform translate-y-4 transition-all duration-700 ease-out flex items-center gap-4">
    <div class="w-12 h-12 bg-[#4896bf] rounded-xl flex items-center justify-center shrink-0 shadow-lg" style="background-color: {{ $pwaThemeColor }}">
        <img src="/pwa-192x192.png" alt="Icon" class="w-8 h-8">
    </div>
    <div class="flex-1">
        <h4 class="text-sm font-bold">Install App</h4>
        <p class="text-[10px] text-slate-400">Add to home screen for immersive experience.</p>
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
@endif

<div id="pwa-offline-indicator" class="fixed top-4 left-1/2 -translate-x-1/2 bg-rose-600/90 backdrop-blur-md text-white px-6 py-2 rounded-full text-xs font-bold shadow-2xl z-[101] opacity-0 pointer-events-none transform -translate-y-4 transition-all duration-500 flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 0a5 5 0 010-7.072M4.95 19.05a9 9 0 010-12.728m0 0l2.829 2.829M4.95 19.05L3 21"/></svg>
    Offline Mode Active
</div>

<script>
    // Hide splash loader when page is ready
    window.addEventListener('load', () => {
        const splash = document.getElementById('app-splash');
        if (splash) {
            splash.style.opacity = '0';
            splash.style.visibility = 'hidden';
            setTimeout(() => splash.remove(), 600);
        }
    });

    let deferredPrompt;
    const banner = document.getElementById('pwa-install-banner');
    const installBtn = document.getElementById('pwa-install');
    const ignoreBtn = document.getElementById('pwa-ignore');
    const offlineIndicator = document.getElementById('pwa-offline-indicator');

    // Service Worker Registration
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/build/sw.js').catch(err => console.log('SW failed', err));
        });
    }

    // Install Prompt Logic
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        
        if (!banner) return;
        
        const lastIgnored = localStorage.getItem('pwa-ignored-at');
        const sevenDaysAgo = Date.now() - (7 * 24 * 60 * 60 * 1000);
        
        if (!lastIgnored || parseInt(lastIgnored) < sevenDaysAgo) {
            setTimeout(() => {
                banner.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                banner.classList.add('opacity-100', 'translate-y-0');
            }, 2000);
        }
    });

    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            hidePwaBanner();
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                localStorage.setItem('pwa-installed', 'true');
            }
            deferredPrompt = null;
        });
    }

    if (ignoreBtn) {
        ignoreBtn.addEventListener('click', () => {
            hidePwaBanner();
            localStorage.setItem('pwa-ignored-at', Date.now().toString());
        });
    }

    function hidePwaBanner() {
        if (!banner) return;
        banner.classList.remove('opacity-100', 'translate-y-0');
        banner.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
    }

    // Offline Detection
    function updateOnlineStatus() {
        if (!offlineIndicator) return;
        if (navigator.onLine) {
            offlineIndicator.classList.remove('opacity-100', 'translate-y-0');
            offlineIndicator.classList.add('opacity-0', 'pointer-events-none', '-translate-y-4');
        } else {
            offlineIndicator.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-4');
            offlineIndicator.classList.add('opacity-100', 'translate-y-0');
        }
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();
</script>
