import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
//    server: {
//        host: '192.168.2.8', // IP de la red
//        port: 3000, // Puerto para el servidor de Vite
//        cors: {
//            origin: 'http://192.168.2.8:8000', // Permite solicitudes desde este origen
//            methods: ['GET', 'POST', 'PUT', 'DELETE'], // Métodos permitidos
//            allowedHeaders: ['Content-Type', 'Authorization'], // Encabezados permitidos
//        },
//    },
});
