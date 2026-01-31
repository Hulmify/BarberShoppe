import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: null,
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,woff,woff2}'],
                navigateFallback: '/',
                cleanupOutdatedCaches: true,
            },

            manifest: {

                name: 'BarberShoppe',
                short_name: 'BarberShoppe',
                description: 'The Ultimate Shop Management Platform',
                theme_color: '#4896bf',
                background_color: '#ffffff',
                display: 'fullscreen',
                orientation: 'portrait',

                categories: ['business', 'productivity', 'lifestyle'],
                icons: [
                    {
                        src: '/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: '/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png'
                    },
                    {
                        src: '/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable'
                    }
                ],
                shortcuts: [
                    {
                        name: 'Schedule',
                        short_name: 'Schedule',
                        description: 'View today\'s appointments',
                        url: '/admin/appointments',
                        icons: [{ src: '/pwa-192x192.png', sizes: '192x192' }]
                    },
                    {
                        name: 'New Booking',
                        short_name: 'Book',
                        description: 'Create a new appointment',
                        url: '/admin/pos',
                        icons: [{ src: '/pwa-192x192.png', sizes: '192x192' }]
                    }
                ]
            }
        })
    ],

    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

