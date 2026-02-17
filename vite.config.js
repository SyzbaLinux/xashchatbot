import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],

    // server: {
    //     host: '0.0.0.0',
    //     port: 5173,
    //     hmr: {
    //         host: '192.168.171.177',
    //         port: 5173,
    //         protocol: 'ws',
    //     },
    //     cors: true,
    // },
});
