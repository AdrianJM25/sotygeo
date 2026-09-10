import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    // AÑADE ESTE BLOQUE SERVER:
    server: {
        host: '0.0.0.0', // Permite conexiones desde tu red WiFi
        hmr: {
            host: '127.0.0.1', // Tu IP real
        },
    },
});