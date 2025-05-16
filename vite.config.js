import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

import fs from "fs"; // fs モジュールは https 設定でのみ使用されるため、条件付きインポートも考えられるが、ここではシンプルにそのままインポート

export default defineConfig(({command, mode}) => { // <-- ここを修正: { command, mode } を受け取る関数形式に変更
    const config = {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
        // server 設定全体を条件付きにするか、または https 部分のみ条件付きにする
        server: {
            host: "dev.subscru.jp", // このホスト設定も開発環境のみの場合がある
            hmr: {
                host: "dev.subscru.jp",
                protocol: "wss",
            },
            cors: true,
            // https 設定を development モードでのみ適用
            ...(mode === 'development' ? { // <-- 条件付きスプレッド構文を使用
                https: {
                    key: fs.readFileSync("/var/www/html/subscru.jp/subscru.key"),
                    cert: fs.readFileSync("/var/www/html/subscru.jp/subscru.crt"),
                },
            } : {}), // development モードでない場合は空のオブジェクトをスプレッド
        },
        // ビルド設定 (通常は mode が production の場合に適用されるデフォルト設定を上書きする場合に使用)
        build: {
            // 例: outDir: 'public/build', // デフォルト設定と同じ場合、記述は必須ではない
        }
    };

    // 必要に応じて mode ごとに別の設定を追加・上書きすることも可能
    // if (mode === 'production') {
    //     // プロダクション固有のビルド設定などを追加
    //     config.build.sourcemap = false;
    // }

    return config;
});
