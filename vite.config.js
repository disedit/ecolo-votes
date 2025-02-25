import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    css: {
        preprocessorOptions: {
            scss: {
            additionalData: `
                @import "/resources/scss/_variables.scss";
                @import 'node_modules/include-media/dist/_include-media.scss';
            `
            }
        }
    },

    plugins: [laravel({
        input: 'resources/js/app.js',
        refresh: true,
    }), vue({
        template: {
            transformAssetUrls: {
                base: null,
                includeAbsolute: false,
            },
        },
    })],

    build: {
        sourcemap: true
    }
});