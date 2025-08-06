import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css'
            ],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
            ],
        }),
    ],
    server: {
        host: '0.0.0.0', // Allow access from outside (e.g., forwarded via SSH)
        port: 5173, // Optional but recommended for consistency
        hmr: {
            host: 'localhost', // This should match what your browser uses (via SSH port forwarding)
            protocol: 'ws',     // Force WebSocket (default is fine, but good to declare)
            port: 5173,         // Match the forwarded port
        },
    },
})
