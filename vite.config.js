import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import dotenv from 'dotenv';

dotenv.config();

const appUrl = (process.env.APP_URL || 'http://localhost').replace(/\/$/, '');
const url = new URL(appUrl);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Optimize for production builds
        minify: 'esbuild',
        cssMinify: true,
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
        // Reduce memory usage during build
        chunkSizeWarningLimit: 1000,
    },
    server: {
        host: true,
        port: 5173,
        strictPort: true,
        hmr: {
            host: url.hostname,
            protocol: url.protocol === 'https:' ? 'wss' : 'ws',
        },
        proxy: {
            // Proxy EVERYTHING except Vite internals to the Laravel app
            // so opening http://localhost:5173 hits your PHP app (Herd/Artisan)
            '^/(?!@vite|@id|node_modules|@fs|resources|build)': {
                target: appUrl,
                changeOrigin: true,
                secure: false,
            },
        },
    },
});