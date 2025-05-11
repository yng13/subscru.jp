import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

import fs from "fs";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: "dev.subscru.jp",
        //https: true,
        https: {
            key: fs.readFileSync("/var/www/html/subscru.jp/subscru.key"),
            cert: fs.readFileSync("/var/www/html/subscru.jp/subscru.crt"),
        },
        hmr: {
            host: "dev.subscru.jp",
            protocol: "wss",
        },
        cors: true,
    },
});
