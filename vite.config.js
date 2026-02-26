import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/monaco-init.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('monaco-editor')) {
                        return 'monaco';
                    }
                }
            },
        },
        minify: 'esbuild',
        cssMinify: true,
        chunkSizeWarningLimit: 5000,
    },
    esbuild: {
        drop: ['console', 'debugger'],
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
