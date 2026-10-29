import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'node:path';

// This build is independent from the legacy Laravel Mix bundle. Its output is
// served by the root router at /minimal/spa, so deploying it is atomic.
export default defineConfig({
    plugins: [vue()],
    base: '/minimal/spa/',
    build: {
        outDir: resolve(__dirname, '../minimal/spa'),
        emptyOutDir: true,
        manifest: 'manifest.json',
        rollupOptions: {
            input: resolve(__dirname, 'resources/spa/main.js'),
            output: {
                entryFileNames: 'app.[hash].js',
                chunkFileNames: 'assets/[name].[hash].js',
                assetFileNames: 'assets/[name].[hash][extname]',
            },
        },
    },
});
