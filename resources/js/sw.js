/* 
  BarberShoppe Service Worker
  Essential for PWA Installation & Push Notifications
*/
import { precacheAndRoute } from 'workbox-precaching';

// 1. Precache generated assets
precacheAndRoute(self.__WB_MANIFEST || []);

// 2. Lifecycle
self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (event) => event.waitUntil(clients.claim()));

// 3. Fetch Handler (REQUIRED FOR INSTALL ICON TO APPEAR)
self.addEventListener('fetch', (event) => {
    // Basic fetch handler to satisfy PWA criteria
    // Workbox handle precached assets automatically, 
    // but having this explicit listener ensures browser compatibility.
});

// 4. Push Notification Listener
self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) return;

    if (event.data) {
        let data = {};
        try {
            data = event.data.json();
        } catch (e) {
            data = { title: 'Notification', body: event.data.text() };
        }

        event.waitUntil(
            self.registration.showNotification(data.title || 'BarberShoppe', {
                body: data.body || 'You have an update.',
                icon: '/pwa-192x192.png',
                badge: '/pwa-192x192.png',
                data: data.data || {},
                requireInteraction: true // Keep it visible until clicked
            })
        );
    }
});

// 5. Notification Click Handler
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
            const url = event.notification.data.url || '/';
            for (let c of list) {
                if (c.url === url && 'focus' in c) return c.focus();
            }
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});
