import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

import fs from "fs";

// Vitest の設定をインポート (vitest.config.js を使用する場合)
import {mergeConfig} from 'vite';
// import vitestConfig from './vitest.config';

export default defineConfig(({command, mode}) => { // <-- ここはそのまま
    const config = {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
        // server 設定
        server: {
            cors: true, // CORS は開発/テストで必要なので常に有効

            // 開発モード (development) でのみ HMR と HTTPS 設定、および開発用ホストを有効にする
            ...(mode === 'development' ? {
                host: "dev.subscru.jp", // 開発用ホスト名
                hmr: { // <-- HMR 設定を条件付きにする
                    host: "dev.subscru.jp",
                    protocol: "wss",
                },
                https: { // <-- HTTPS 設定 (既に条件付きですが、確認)
                    key: fs.readFileSync("/var/www/html/subscru.jp/subscru.key"),
                    cert: fs.readFileSync("/var/www/html/subscru.jp/subscru.crt"),
                },
            } : {
                // 開発モード以外 (production, test など) では HMR, HTTPS は無効
                // ホストも開発用ではないものを指定するか、またはデフォルトに任せる
                // CI環境やプロダクションでは、通常 localhost などで十分です
                host: 'localhost', // 例: テスト環境やプロダクションでは localhost を使う
            }),
        },
        // build 設定 (デフォルトを使用するか、productionモードでのみ適用する設定を記述)
        build: {
            // ...
        }
    };

    // Vitest の設定をテストモードでのみマージする (vitest.config.js を使用しない場合)
    // vitest.config.js を使用する場合は、そちらの設定ファイルで test ブロックを定義し、mergeConfig を使用します。
    if (mode === 'test') {
        // Vitest の設定をここに直接記述するか、別途 vitest.config.js を用意して mergeConfig でマージ
        // 例: vitest.config.js を別途用意している場合
        // import vitestConfig from './vitest.config';
        // return mergeConfig(config, vitestConfig);

        // Vitest の設定をここに直接記述する場合
        return mergeConfig(config, {
            test: {
                globals: true,
                environment: 'jsdom',
                // coverage: { enabled: true },
                // setupFiles: './tests/js/setup.js',
                // testMatch: ['resources/js/**/*.test.js'],
            }
        });
    }


    return config; // 開発モードまたはプロダクションモードの場合
});
