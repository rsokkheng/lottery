import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // 'resources/scss/app.scss'
            ],
            refresh: true,
        }),
    ],
    base: process.env.ASSET_URL || '/',
    content: [
        './resources/**/*.blade.php',
        './vendor/masmerise/livewire-toaster/resources/views/*.blade.php', // 👈
    ],
});
